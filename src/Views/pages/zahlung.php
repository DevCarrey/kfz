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
    (string)(
        $paymentConfig['currency'] ?? 'EUR'
    )
);

$paymentAmount = number_format(
    $amountCents / 100,
    2,
    ',',
    '.'
);

$paymentApplicationId = (int)(
    $_SESSION['pending_payment_application_id'] ?? 0
);

$paymentReference = (string)(
    $_SESSION['pending_payment_reference'] ?? ''
);

if ($paymentApplicationId <= 0):
?>

<section class="kfz-section">
    <div class="container">

        <div
            class="kfz-process-error"
            role="alert"
        >
            Es wurde kein offener Zahlungsvorgang gefunden.
        </div>

        <a
            href="<?= $escape($url('/')) ?>"
            class="kfz-button kfz-button-primary"
        >
            Zur Startseite
        </a>

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
?>

<section
    class="kfz-section kfz-payment-page"
    aria-labelledby="payment-title"
>
    <div class="container">

        <div class="kfz-section-header">

            <span class="kfz-section-kicker">
                Testzahlung
            </span>

            <h1
                id="payment-title"
                class="kfz-section-title"
            >
                Zahlung abschließen
            </h1>

            <p class="kfz-section-text">
                Schließen Sie die Testzahlung für Ihre
                Fahrzeugabmeldung ab.
            </p>

        </div>


        <div class="kfz-process-form-wrapper">

            <div class="kfz-process-info-card">

                <div>
                    <strong>
                        Fahrzeugabmeldung
                    </strong>

                    <p>
                        Vorgangsnummer:
                        <strong>
                            <?= $escape($paymentReference) ?>
                        </strong>
                    </p>
                </div>

            </div>


            <div class="kfz-payment-summary">

                <div class="kfz-payment-summary-row">
                    <span>
                        Servicegebühr
                    </span>

                    <strong>
                        <?= $escape($paymentAmount) ?>
                        <?= $escape($currency) ?>
                    </strong>
                </div>

                <div class="kfz-payment-summary-row">
                    <span>
                        Zahlungsart
                    </span>

                    <strong>
                        Testzahlung
                    </strong>
                </div>

            </div>


            <div class="kfz-process-info-card">

                <strong>
                    Hinweis zum Testbetrieb
                </strong>

                <p>
                    Es findet keine echte Abbuchung statt.
                    Diese Testzahlung simuliert lediglich den späteren
                    Zahlungsablauf.
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