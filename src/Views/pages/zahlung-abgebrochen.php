<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Support/view_helpers.php';

$url = static fn (string $path): string => kfz_url($path);
$escape = static fn (mixed $value): string => kfz_escape($value);

$applicationId = (int)(
    $_SESSION['pending_payment_application_id'] ?? 0
);

if ($applicationId > 0) {
    try {
        $pdo = require __DIR__ . '/../../Config/database.php';

        $statement = $pdo->prepare(
            'UPDATE payments
             SET status = :status
             WHERE application_id = :application_id
               AND status = :open_status'
        );

        $statement->execute([
            'status' => 'abgebrochen',
            'application_id' => $applicationId,
            'open_status' => 'offen',
        ]);

        $applicationStatement = $pdo->prepare(
            'UPDATE applications
             SET status = :status
             WHERE id = :application_id
               AND status = :open_status'
        );

        $applicationStatement->execute([
            'status' => 'storniert',
            'application_id' => $applicationId,
            'open_status' => 'zahlung_offen',
        ]);
    } catch (Throwable $exception) {
        // Keine technischen Details an den Besucher ausgeben.
    }
}

unset(
    $_SESSION['pending_payment_application_id'],
    $_SESSION['pending_payment_reference']
);
?>

<section class="kfz-section">
    <div class="container">

        <div
            class="kfz-process-error"
            role="alert"
        >
            <h1>
                Zahlung abgebrochen
            </h1>

            <p>
                Die Testzahlung wurde abgebrochen.
                Der Vorgang wurde nicht abgeschlossen.
            </p>

            <div class="kfz-process-form-actions">

                <a
                    href="<?= $escape($url('/')) ?>"
                    class="kfz-button kfz-button-primary"
                >
                    Neue Abmeldung starten
                </a>

            </div>
        </div>

    </div>
</section>