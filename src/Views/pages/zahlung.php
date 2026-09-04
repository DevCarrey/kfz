<?php
declare(strict_types=1);

/**
 * Kfz Digital – Zahlung
 *
 * Aktuell:
 * - Mock-Zahlung
 * - keine echte Abbuchung
 * - Zahlungsstatus wird gespeichert
 * - payment_confirmed-E-Mail-Job wird erzeugt
 * - api_submit-Job wird erzeugt
 * - doppelte Jobs werden verhindert
 *
 * Ablauf:
 *
 * 1. Zahlungsvorgang aus Session laden
 * 2. Mock-Zahlung bestätigen
 * 3. Zahlung auf "bezahlt" setzen
 * 4. Anwendung auf "api_warteschlange" setzen
 * 5. Zahlungs-E-Mail-Job anlegen
 * 6. API-Submit-Job anlegen
 * 7. Zur Erfolgsseite weiterleiten
 */

require_once __DIR__ . '/../../Support/view_helpers.php';

$url = static fn (string $path): string => kfz_url($path);

$escape = static fn (mixed $value): string => kfz_escape($value);


/*
|--------------------------------------------------------------------------
| Hilfsfunktionen
|--------------------------------------------------------------------------
*/

$getInput = static function (
    array $source,
    string $key,
    string $default = ''
): string {
    $value = $source[$key] ?? $default;

    if (!is_scalar($value)) {
        return $default;
    }

    return trim((string)$value);
};

$redirect = static function (
    string $location
): never {
    if (!headers_sent()) {
        header(
            'Location: ' . $location,
            true,
            303
        );

        exit;
    }

    $safeLocation = htmlspecialchars(
        $location,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );

    $jsonLocation = json_encode(
        $location,
        JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    );

    echo '<meta http-equiv="refresh" content="0;url='
        . $safeLocation
        . '">';

    echo '<script>';
    echo 'window.location.replace(';
    echo $jsonLocation;
    echo ');';
    echo '</script>';

    exit;
};

$addError = static function (
    array &$errors,
    string $message
): void {
    if (!in_array($message, $errors, true)) {
        $errors[] = $message;
    }
};


/*
|--------------------------------------------------------------------------
| Datenbank laden
|--------------------------------------------------------------------------
*/

try {
    $pdo = require __DIR__ . '/../../Config/database.php';

    if (!$pdo instanceof PDO) {
        throw new RuntimeException(
            'Ungültige Datenbankverbindung.'
        );
    }
} catch (Throwable $exception) {
    ?>
    <section class="kfz-section">
        <div class="container">
            <div
                class="kfz-process-error"
                role="alert"
            >
                Die Datenbankverbindung konnte nicht hergestellt werden.
            </div>
        </div>
    </section>
    <?php
    return;
}


/*
|--------------------------------------------------------------------------
| Zahlungs-Konfiguration laden
|--------------------------------------------------------------------------
*/

$paymentConfigFile = __DIR__ . '/../../Config/payment.php';

$paymentConfig = is_file($paymentConfigFile)
    ? require $paymentConfigFile
    : [
        'mode' => 'mock',
        'amount_cents' => 4990,
        'currency' => 'EUR',
    ];

$paymentMode = strtolower(
    (string)($paymentConfig['mode'] ?? 'mock')
);

$defaultAmountCents = (int)(
    $paymentConfig['amount_cents'] ?? 4990
);

$defaultCurrency = strtoupper(
    (string)($paymentConfig['currency'] ?? 'EUR')
);

if ($defaultAmountCents <= 0) {
    $defaultAmountCents = 4990;
}

if ($defaultCurrency === '') {
    $defaultCurrency = 'EUR';
}


/*
|--------------------------------------------------------------------------
| CSRF-Token
|--------------------------------------------------------------------------
*/

if (
    empty($_SESSION['process_csrf_token'])
    || !is_string($_SESSION['process_csrf_token'])
) {
    $_SESSION['process_csrf_token'] = bin2hex(
        random_bytes(32)
    );
}

$csrfToken = (string)$_SESSION['process_csrf_token'];


/*
|--------------------------------------------------------------------------
| Zahlungsvorgang aus Session laden
|--------------------------------------------------------------------------
*/

$applicationId = (int)(
    $_SESSION['pending_payment_application_id'] ?? 0
);

$sessionReferenceNumber = trim(
    (string)(
        $_SESSION['pending_payment_reference'] ?? ''
    )
);

$paymentError = '';
$application = null;
$payment = null;
$referenceNumber = $sessionReferenceNumber;


/*
|--------------------------------------------------------------------------
| Eindeutigen Job anlegen
|--------------------------------------------------------------------------
*/

$createJob = static function (
    PDO $pdo,
    int $applicationId,
    string $jobType,
    array $payload = [],
    int $maxAttempts = 5
): void {
    $existingStatement = $pdo->prepare(
        'SELECT
            id,
            status,
            payload_json
         FROM application_jobs
         WHERE application_id = :application_id
           AND job_type = :job_type
           AND status IN
           (
                :open_status,
                :processing_status,
                :successful_status
           )
         ORDER BY id DESC
         LIMIT 20'
    );

    $existingStatement->execute([
        'application_id' => $applicationId,
        'job_type' => $jobType,
        'open_status' => 'offen',
        'processing_status' => 'in_bearbeitung',
        'successful_status' => 'erfolgreich',
    ]);

    $existingJobs = $existingStatement->fetchAll();

    if (is_array($existingJobs)) {
        foreach ($existingJobs as $existingJob) {
            $existingPayload = [];

            if (!empty($existingJob['payload_json'])) {
                $decodedPayload = json_decode(
                    (string)$existingJob['payload_json'],
                    true
                );

                if (is_array($decodedPayload)) {
                    $existingPayload = $decodedPayload;
                }
            }

            if ($existingPayload === $payload) {
                return;
            }

            /*
             * Für api_submit genügt ein erfolgreicher oder offener
             * Job unabhängig vom Payload.
             */
            if (
                $jobType === 'api_submit'
                && in_array(
                    (string)($existingJob['status'] ?? ''),
                    [
                        'offen',
                        'in_bearbeitung',
                        'erfolgreich',
                    ],
                    true
                )
            ) {
                return;
            }
        }
    }

    $payloadJson = null;

    if ($payload !== []) {
        $payloadJson = json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_THROW_ON_ERROR
        );
    }

    $insertStatement = $pdo->prepare(
        'INSERT INTO application_jobs
        (
            application_id,
            job_type,
            status,
            attempts,
            max_attempts,
            available_at,
            payload_json
        )
        VALUES
        (
            :application_id,
            :job_type,
            :status,
            0,
            :max_attempts,
            NOW(),
            :payload_json
        )'
    );

    $insertStatement->execute([
        'application_id' => $applicationId,
        'job_type' => $jobType,
        'status' => 'offen',
        'max_attempts' => $maxAttempts,
        'payload_json' => $payloadJson,
    ]);
};


/*
|--------------------------------------------------------------------------
| Zahlung bestätigen
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && $getInput($_POST, 'payment_action') === 'complete'
) {
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($submittedToken)
        || $submittedToken === ''
        || !hash_equals($csrfToken, $submittedToken)
    ) {
        $paymentError =
            'Die Sitzung ist abgelaufen. Bitte starten Sie den Vorgang erneut.';
    }

    if (
        $paymentError === ''
        && $applicationId <= 0
    ) {
        $paymentError =
            'Es wurde kein offener Zahlungsvorgang gefunden.';
    }

    if ($paymentError === '') {
        try {
            $pdo->beginTransaction();

            /*
             * Vorgang laden
             */
            $applicationStatement = $pdo->prepare(
                'SELECT
                    id,
                    reference_number,
                    process_type,
                    status,
                    license_plate,
                    first_name,
                    last_name,
                    email
                 FROM applications
                 WHERE id = :application_id
                 LIMIT 1'
            );

            $applicationStatement->execute([
                'application_id' => $applicationId,
            ]);

            $application = $applicationStatement->fetch();

            if (!is_array($application)) {
                throw new RuntimeException(
                    'Der Vorgang wurde nicht gefunden.'
                );
            }

            $referenceNumber = trim(
                (string)(
                    $application['reference_number'] ?? ''
                )
            );

            $oldApplicationStatus = (string)(
                $application['status'] ?? ''
            );

            /*
             * Zahlung laden
             */
            $paymentStatement = $pdo->prepare(
                'SELECT
                    id,
                    provider,
                    amount_cents,
                    currency,
                    status,
                    paid_at
                 FROM payments
                 WHERE application_id = :application_id
                 ORDER BY id DESC
                 LIMIT 1'
            );

            $paymentStatement->execute([
                'application_id' => $applicationId,
            ]);

            $payment = $paymentStatement->fetch();

            if (!is_array($payment)) {
                throw new RuntimeException(
                    'Für den Vorgang wurde keine Zahlung gefunden.'
                );
            }

            $paymentId = (int)(
                $payment['id'] ?? 0
            );

            $paymentStatus = (string)(
                $payment['status'] ?? ''
            );

            if ($paymentId <= 0) {
                throw new RuntimeException(
                    'Die Zahlungsdaten sind ungültig.'
                );
            }

            /*
             * Bereits bezahlten Vorgang nicht erneut bearbeiten
             */
            if ($paymentStatus === 'bezahlt') {
                $pdo->commit();

                $_SESSION['paid_application_id'] =
                    $applicationId;

                $_SESSION['paid_reference_number'] =
                    $referenceNumber;

                unset(
                    $_SESSION['pending_payment_application_id'],
                    $_SESSION['pending_payment_reference']
                );

                $redirect(
                    $url('/zahlung-erfolgreich/')
                );
            }

            if ($paymentStatus !== 'offen') {
                throw new RuntimeException(
                    'Dieser Zahlungsvorgang ist nicht mehr offen.'
                );
            }

            /*
             * Mock-Zahlung als bezahlt markieren
             */
            $paymentUpdate = $pdo->prepare(
                "UPDATE payments
                 SET
                    status = 'bezahlt',
                    paid_at = NOW()
                 WHERE id = :payment_id
                   AND status = 'offen'"
            );

            $paymentUpdate->execute([
                'payment_id' => $paymentId,
            ]);

            if ($paymentUpdate->rowCount() !== 1) {
                throw new RuntimeException(
                    'Die Zahlung konnte nicht bestätigt werden.'
                );
            }

            /*
             * Vorgang für API-Übermittlung vormerken
             */
            $applicationUpdate = $pdo->prepare(
                "UPDATE applications
                 SET
                    status = 'api_warteschlange',
                    submitted_at = COALESCE(
                        submitted_at,
                        NOW()
                    ),
                    api_error = NULL
                 WHERE id = :application_id
                   AND status IN
                   (
                        'zahlung_offen',
                        'bezahlt',
                        'eingegangen'
                   )"
            );

            $applicationUpdate->execute([
                'application_id' => $applicationId,
            ]);

            /*
             * Statushistorie Zahlung
             */
            $historyStatement = $pdo->prepare(
                'INSERT INTO application_status_history
                (
                    application_id,
                    old_status,
                    new_status,
                    comment
                )
                VALUES
                (
                    :application_id,
                    :old_status,
                    :new_status,
                    :comment
                )'
            );

            $historyStatement->execute([
                'application_id' => $applicationId,
                'old_status' => $oldApplicationStatus,
                'new_status' => 'api_warteschlange',
                'comment' => $paymentMode === 'mock'
                    ? 'Testzahlung erfolgreich bestätigt. Vorgang wartet auf die automatische API-Übermittlung.'
                    : 'Zahlung erfolgreich bestätigt. Vorgang wartet auf die automatische API-Übermittlung.',
            ]);

            /*
             * Zahlungs-E-Mail-Job
             */
            $createJob(
                $pdo,
                $applicationId,
                'send_email',
                [
                    'notification_type' => 'payment_confirmed',
                ],
                5
            );

            /*
             * API-Submit-Job
             */
            $createJob(
                $pdo,
                $applicationId,
                'api_submit',
                [],
                5
            );

            $pdo->commit();

            /*
             * Temporäre Sessiondaten löschen
             */
            unset(
                $_SESSION['pending_payment_application_id'],
                $_SESSION['pending_payment_reference']
            );

            $_SESSION['paid_application_id'] =
                $applicationId;

            $_SESSION['paid_reference_number'] =
                $referenceNumber;

            $_SESSION['process_csrf_token'] = bin2hex(
                random_bytes(32)
            );

            $redirect(
                $url('/zahlung-erfolgreich/')
            );
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $paymentError =
                'Die Zahlung konnte nicht verarbeitet werden.';
        }
    }
}


/*
|--------------------------------------------------------------------------
| Vorgang und Zahlung für Anzeige laden
|--------------------------------------------------------------------------
*/

if ($applicationId > 0) {
    try {
        $applicationStatement = $pdo->prepare(
            'SELECT
                id,
                reference_number,
                process_type,
                status,
                license_plate,
                first_name,
                last_name,
                email
             FROM applications
             WHERE id = :application_id
             LIMIT 1'
        );

        $applicationStatement->execute([
            'application_id' => $applicationId,
        ]);

        $application = $applicationStatement->fetch();

        if (is_array($application)) {
            $referenceNumber = trim(
                (string)(
                    $application['reference_number'] ?? ''
                )
            );
        }

        $paymentStatement = $pdo->prepare(
            'SELECT
                id,
                provider,
                amount_cents,
                currency,
                status,
                paid_at
             FROM payments
             WHERE application_id = :application_id
             ORDER BY id DESC
             LIMIT 1'
        );

        $paymentStatement->execute([
            'application_id' => $applicationId,
        ]);

        $payment = $paymentStatement->fetch();
    } catch (Throwable $exception) {
        $application = null;
        $payment = null;

        if ($paymentError === '') {
            $paymentError =
                'Der Zahlungsvorgang konnte nicht geladen werden.';
        }
    }
}


/*
|--------------------------------------------------------------------------
| Kein Zahlungsvorgang vorhanden
|--------------------------------------------------------------------------
*/

if (
    !is_array($application)
    || !is_array($payment)
):
?>

<section class="kfz-section">
    <div class="container">

        <div
            class="kfz-process-error"
            role="alert"
        >
            <h1>
                Kein Zahlungsvorgang gefunden
            </h1>

            <p>
                Es wurde kein offener Zahlungsvorgang gefunden.
                Bitte starten Sie die Fahrzeugabmeldung erneut.
            </p>

            <a
                href="<?= $escape($url('/')) ?>"
                class="kfz-button kfz-button-primary"
            >
                Zur Startseite
            </a>
        </div>

    </div>
</section>

<?php
return;
endif;


/*
|--------------------------------------------------------------------------
| Bereits bezahlt
|--------------------------------------------------------------------------
*/

if (
    (string)($payment['status'] ?? '') === 'bezahlt'
) {
    $_SESSION['paid_application_id'] =
        $applicationId;

    $_SESSION['paid_reference_number'] =
        $referenceNumber;

    unset(
        $_SESSION['pending_payment_application_id'],
        $_SESSION['pending_payment_reference']
    );

    $redirect(
        $url('/zahlung-erfolgreich/')
    );
}

$paymentAmountCents = (int)(
    $payment['amount_cents'] ?? $defaultAmountCents
);

$paymentCurrency = strtoupper(
    (string)(
        $payment['currency'] ?? $defaultCurrency
    )
);

$paymentAmount = number_format(
    $paymentAmountCents / 100,
    2,
    ',',
    '.'
);
?>

<section
    class="kfz-section kfz-payment-page"
    aria-labelledby="payment-title"
>
    <div class="container">

        <div class="kfz-section-header">

            <span class="kfz-section-kicker">
                Zahlung
            </span>

            <h1
                id="payment-title"
                class="kfz-section-title"
            >
                Zahlung abschließen
            </h1>

            <p class="kfz-section-text">
                Schließen Sie die Zahlung für Ihre Fahrzeugabmeldung ab.
            </p>

        </div>


        <?php if ($paymentError !== ''): ?>

            <div
                class="kfz-process-error"
                role="alert"
            >
                <h2>
                    Zahlung konnte nicht verarbeitet werden
                </h2>

                <p>
                    <?= $escape($paymentError) ?>
                </p>
            </div>

        <?php endif; ?>


        <div class="kfz-process-form-wrapper">

            <div class="kfz-process-form-header">

                <div>
                    <span class="kfz-section-kicker">
                        Vorgang
                    </span>

                    <h2>
                        Fahrzeugabmeldung
                    </h2>

                    <p>
                        Vorgangsnummer:
                        <strong>
                            <?= $escape($referenceNumber) ?>
                        </strong>
                    </p>
                </div>

            </div>


            <div class="kfz-payment-summary">

                <div class="kfz-payment-summary-row">
                    <span>
                        Leistung
                    </span>

                    <strong>
                        Fahrzeugabmeldung
                    </strong>
                </div>

                <div class="kfz-payment-summary-row">
                    <span>
                        Kennzeichen
                    </span>

                    <strong>
                        <?= $escape(
                            $application['license_plate'] ?? ''
                        ) ?>
                    </strong>
                </div>

                <div class="kfz-payment-summary-row">
                    <span>
                        Gesamtbetrag
                    </span>

                    <strong>
                        <?= $escape($paymentAmount) ?>
                        <?= $escape($paymentCurrency) ?>
                    </strong>
                </div>

            </div>


            <?php if ($paymentMode === 'mock'): ?>

                <div class="kfz-process-info-card">

                    <strong>
                        Testbetrieb
                    </strong>

                    <p>
                        Es findet keine echte Abbuchung statt.
                        Die Zahlung wird nur simuliert.
                    </p>

                </div>

            <?php else: ?>

                <div class="kfz-process-info-card">

                    <strong>
                        Zahlung
                    </strong>

                    <p>
                        Die sichere Zahlungsabwicklung ist vorbereitet.
                    </p>

                </div>

            <?php endif; ?>


            <form
                action="<?= $escape($url('/zahlung/')) ?>"
                method="post"
                class="kfz-process-form"
            >

                <input
                    type="hidden"
                    name="payment_action"
                    value="complete"
                >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= $escape($csrfToken) ?>"
                >

                <div class="kfz-process-form-actions">

                    <a
                        href="<?= $escape(
                            $url(
                                '/zahlung-abgebrochen/'
                            )
                        ) ?>"
                        class="kfz-button kfz-button-outline"
                    >
                        Zahlung abbrechen
                    </a>

                    <button
                        type="submit"
                        class="kfz-button kfz-button-primary"
                    >
                        <?php if ($paymentMode === 'mock'): ?>
                            Testzahlung bestätigen
                        <?php else: ?>
                            Zahlung starten
                        <?php endif; ?>
                    </button>

                </div>

            </form>

        </div>

    </div>
</section>