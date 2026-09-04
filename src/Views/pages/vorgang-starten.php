<?php
declare(strict_types=1);

/**
 * Kfz Digital – Zahlung starten
 *
 * Aktuell:
 * - Mock-Zahlung
 * - Keine echte Abbuchung
 * - Kein Stripe erforderlich
 * - Zahlung wird nach Bestätigung als bezahlt gespeichert
 */

require_once __DIR__ . '/../../Support/view_helpers.php';

$url = static fn (string $path): string => kfz_url($path);

$escape = static fn (mixed $value): string => kfz_escape($value);


/*
|--------------------------------------------------------------------------
| Datenbank laden
|--------------------------------------------------------------------------
*/

try {
    $pdo = require __DIR__ . '/../../Config/database.php';
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

if (is_file($paymentConfigFile)) {
    $paymentConfig = require $paymentConfigFile;
} else {
    $paymentConfig = [
        'mode' => 'mock',
        'amount_cents' => 4990,
        'currency' => 'EUR',
    ];
}

$paymentMode = strtolower(
    (string)($paymentConfig['mode'] ?? 'mock')
);

$amountCents = (int)(
    $paymentConfig['amount_cents'] ?? 4990
);

$currency = strtoupper(
    (string)($paymentConfig['currency'] ?? 'EUR')
);

if ($amountCents <= 0) {
    $amountCents = 4990;
}

if ($currency === '') {
    $currency = 'EUR';
}

$formattedAmount = number_format(
    $amountCents / 100,
    2,
    ',',
    '.'
);


/*
|--------------------------------------------------------------------------
| Offenen Zahlungsvorgang aus der Session lesen
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
$paymentCompleted = false;
$referenceNumber = $sessionReferenceNumber;


/*
|--------------------------------------------------------------------------
| CSRF-Token erzeugen
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
| Zahlung verarbeiten
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['payment_action'] ?? '') === 'complete'
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
             * Vorgang und aktuelle Zahlung laden
             */
            $applicationStatement = $pdo->prepare(
                'SELECT
                    id,
                    reference_number,
                    status
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

            $referenceNumber = (string)(
                $application['reference_number'] ?? ''
            );

            $applicationStatus = (string)(
                $application['status'] ?? ''
            );

            /*
             * Zahlung laden
             */
            $paymentStatement = $pdo->prepare(
                'SELECT
                    id,
                    status,
                    amount_cents,
                    currency
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
                    'Für diesen Vorgang wurde keine Zahlung gefunden.'
                );
            }

            $paymentId = (int)($payment['id'] ?? 0);
            $paymentStatus = (string)(
                $payment['status'] ?? ''
            );

            if ($paymentId <= 0) {
                throw new RuntimeException(
                    'Die Zahlungsdaten sind ungültig.'
                );
            }

            /*
             * Doppelte Bestätigung verhindern
             */
            if ($paymentStatus === 'bezahlt') {
                $pdo->commit();

                $_SESSION['paid_reference_number'] =
                    $referenceNumber;

                unset(
                    $_SESSION['pending_payment_application_id'],
                    $_SESSION['pending_payment_reference']
                );

                header(
                    'Location: ' . $url('/zahlung-erfolgreich/'),
                    true,
                    303
                );

                exit;
            }

            if ($paymentStatus !== 'offen') {
                throw new RuntimeException(
                    'Dieser Zahlungsvorgang ist nicht mehr offen.'
                );
            }

            /*
             * Zahlung auf bezahlt setzen
             */
            $updatePaymentStatement = $pdo->prepare(
                'UPDATE payments
                 SET
                    status = :paid_status,
                    paid_at = NOW()
                 WHERE id = :payment_id
                   AND status = :open_status'
            );

            $updatePaymentStatement->execute([
                'paid_status' => 'bezahlt',
                'payment_id' => $paymentId,
                'open_status' => 'offen',
            ]);

            if ($updatePaymentStatement->rowCount() !== 1) {
                throw new RuntimeException(
                    'Die Zahlung wurde bereits verarbeitet.'
                );
            }

            /*
             * Anwendung auf bezahlt setzen
             */
            $updateApplicationStatement = $pdo->prepare(
                'UPDATE applications
                 SET
                    status = :new_status,
                    submitted_at = COALESCE(
                        submitted_at,
                        NOW()
                    )
                 WHERE id = :application_id
                   AND status = :old_status'
            );

            $updateApplicationStatement->execute([
                'new_status' => 'bezahlt',
                'application_id' => $applicationId,
                'old_status' => 'zahlung_offen',
            ]);

            /*
             * Falls dein Vorgang bereits einen anderen Status hat,
             * bleibt die Zahlung trotzdem gespeichert.
             */
            if (
                $updateApplicationStatement->rowCount() === 0
                && $applicationStatus !== 'bezahlt'
            ) {
                $fallbackApplicationStatement = $pdo->prepare(
                    'UPDATE applications
                     SET
                        status = :new_status,
                        submitted_at = COALESCE(
                            submitted_at,
                            NOW()
                        )
                     WHERE id = :application_id'
                );

                $fallbackApplicationStatement->execute([
                    'new_status' => 'bezahlt',
                    'application_id' => $applicationId,
                ]);
            }

            /*
             * Statushistorie schreiben
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
                'old_status' => $applicationStatus,
                'new_status' => 'bezahlt',
                'comment' =>
                    $paymentMode === 'mock'
                        ? 'Testzahlung erfolgreich bestätigt.'
                        : 'Zahlung erfolgreich bestätigt.',
            ]);

            $pdo->commit();

            $_SESSION['paid_reference_number'] =
                $referenceNumber;

            $_SESSION['paid_application_id'] =
                $applicationId;

            unset(
                $_SESSION['pending_payment_application_id'],
                $_SESSION['pending_payment_reference']
            );

            $_SESSION['process_csrf_token'] = bin2hex(
                random_bytes(32)
            );

            header(
                'Location: ' . $url('/zahlung-erfolgreich/'),
                true,
                303
            );

            exit;
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
| Prüfen, ob ein offener Zahlungsvorgang vorhanden ist
|--------------------------------------------------------------------------
*/

$application = null;
$payment = null;

if ($applicationId > 0) {
    try {
        $applicationStatement = $pdo->prepare(
            'SELECT
                id,
                reference_number,
                status,
                license_plate,
                process_type
             FROM applications
             WHERE id = :application_id
             LIMIT 1'
        );

        $applicationStatement->execute([
            'application_id' => $applicationId,
        ]);

        $application = $applicationStatement->fetch();

        if (is_array($application)) {
            $referenceNumber = (string)(
                $application['reference_number'] ?? ''
            );
        }

        $paymentStatement = $pdo->prepare(
            'SELECT
                id,
                status,
                amount_cents,
                currency
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
    }
}


/*
|--------------------------------------------------------------------------
| Kein Zahlungsvorgang gefunden
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
    $_SESSION['paid_reference_number'] =
        $referenceNumber;

    unset(
        $_SESSION['pending_payment_application_id'],
        $_SESSION['pending_payment_reference']
    );

    header(
        'Location: ' . $url('/zahlung-erfolgreich/'),
        true,
        303
    );

    exit;
}

$paymentAmountCents = (int)(
    $payment['amount_cents'] ?? $amountCents
);

$paymentCurrency = strtoupper(
    (string)(
        $payment['currency'] ?? $currency
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
                        Diese Seite simuliert den späteren
                        Zahlungsablauf.
                    </p>

                </div>

            <?php else: ?>

                <div class="kfz-process-info-card">

                    <strong>
                        Zahlung
                    </strong>

                    <p>
                        Sie werden zur sicheren Zahlungsabwicklung
                        weitergeleitet.
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
                            $url('/zahlung-abgebrochen/')
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
