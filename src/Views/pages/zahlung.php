<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Support/view_helpers.php';

$url = static fn (string $path): string => kfz_url($path);
$escape = static fn (mixed $value): string => kfz_escape($value);

$paymentConfig = require __DIR__ . '/../../Config/payment.php';

$amountCents = (int)(
    $paymentConfig['amount_cents'] ?? 4990
);

$currency = strtoupper(
    (string)($paymentConfig['currency'] ?? 'EUR')
);

$amount = number_format(
    $amountCents / 100,
    2,
    ',',
    '.'
);

$applicationId = (int)(
    $_SESSION['pending_payment_application_id'] ?? 0
);

$referenceNumber = (string)(
    $_SESSION['pending_payment_reference'] ?? ''
);

if (
    $applicationId <= 0
    || $referenceNumber === ''
):
?>

<section class="kfz-section">
    <div class="container">

        <div class="kfz-process-error" role="alert">
            <h1>
                Kein Zahlungsvorgang gefunden
            </h1>

            <p>
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
| Mockzahlung bestätigen
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
            'Die Sitzung ist abgelaufen. Bitte starten Sie erneut.';
    } else {
        try {
            $pdo = require __DIR__ . '/../../Config/database.php';

            $pdo->beginTransaction();

            $paymentStatement = $pdo->prepare(
                'UPDATE payments
                 SET
                    status = :paid_status,
                    paid_at = NOW()
                 WHERE application_id = :application_id
                   AND status = :open_status'
            );

            $paymentStatement->execute([
                'paid_status' => 'bezahlt',
                'application_id' => $applicationId,
                'open_status' => 'offen',
            ]);

            if ($paymentStatement->rowCount() !== 1) {
                throw new RuntimeException(
                    'Die Zahlung ist nicht mehr offen.'
                );
            }

            $applicationStatement = $pdo->prepare(
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

            $applicationStatement->execute([
                'new_status' => 'bezahlt',
                'application_id' => $applicationId,
                'old_status' => 'zahlung_offen',
            ]);

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
                'old_status' => 'zahlung_offen',
                'new_status' => 'bezahlt',
                'comment' => 'Mockzahlung erfolgreich bestätigt.',
            ]);

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
        } catch (Throwable $exception) {
            if (
                isset($pdo)
                && $pdo instanceof PDO
                && $pdo->inTransaction()
            ) {
                $pdo->rollBack();
            }

            $paymentError =
                'Die Zahlung konnte nicht verarbeitet werden.';
        }
    }
}
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
                Schließen Sie die Zahlung für Ihre
                Fahrzeugabmeldung ab.
            </p>

        </div>


        <?php if (!empty($paymentError)): ?>

            <div
                class="kfz-process-error"
                role="alert"
            >
                <?= $escape($paymentError) ?>
            </div>

        <?php endif; ?>


        <div class="kfz-process-form-wrapper">

            <div class="kfz-payment-summary">

                <div class="kfz-payment-summary-row">
                    <span>
                        Vorgangsnummer
                    </span>

                    <strong>
                        <?= $escape($referenceNumber) ?>
                    </strong>
                </div>

                <div class="kfz-payment-summary-row">
                    <span>
                        Fahrzeugabmeldung
                    </span>

                    <strong>
                        <?= $escape($amount) ?>
                        <?= $escape($currency) ?>
                    </strong>
                </div>

            </div>


            <div class="kfz-process-info-card">

                <strong>
                    Testbetrieb
                </strong>

                <p>
                    Es findet keine echte Abbuchung statt.
                    Die Zahlung wird nur simuliert.
                </p>

            </div>


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
                        Testzahlung bestätigen
                    </button>

                </div>

            </form>

        </div>

    </div>
</section>