<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Support/view_helpers.php';

$url = static fn (string $path): string => kfz_url($path);
$escape = static fn (mixed $value): string => kfz_escape($value);

$referenceNumber = (string)(
    $_SESSION['paid_reference_number'] ?? ''
);

if ($referenceNumber === '') {
    $referenceNumber = 'wird noch geladen';
}
?>

<section class="kfz-section">
    <div class="container">

        <div
            class="kfz-process-success"
            role="status"
            aria-live="polite"
        >
            <h1>
                Zahlung erfolgreich
            </h1>

            <p>
                Ihre Testzahlung wurde erfolgreich bestätigt.
            </p>

            <p>
                Ihr Vorgang wird nun weiterbearbeitet.
            </p>

            <p class="kfz-application-number">
                Vorgangsnummer:
                <strong>
                    <?= $escape($referenceNumber) ?>
                </strong>
            </p>

            <div class="kfz-process-form-actions">

                <a
                    href="<?= $escape(
                        $url('/vorgang-pruefen/')
                    ) ?>"
                    class="kfz-button kfz-button-primary"
                >
                    Vorgang prüfen
                </a>

                <a
                    href="<?= $escape($url('/')) ?>"
                    class="kfz-button kfz-button-outline"
                >
                    Zur Startseite
                </a>

            </div>
        </div>

    </div>
</section>
