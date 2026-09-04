<?php
declare(strict_types=1);

$url = static fn (string $path): string => kfz_url($path);
$escape = static fn (mixed $value): string => kfz_escape($value);

$GLOBALS['render_process_on_home'] = true;
?>

<section class="kfz-hero">

    <div class="container">

        <div class="kfz-hero-layout">

            <div class="kfz-hero-content">

                <span class="kfz-section-kicker">
                    Ohne Registrierung
                </span>

                <h1 class="kfz-hero-title">
                    Fahrzeug abmelden.
                    Einfach digital.
                </h1>

                <p class="kfz-hero-text">
                    Reichen Sie Ihre Fahrzeugabmeldung direkt online ein –
                    übersichtlich, schnell und ohne Kundenkonto.
                </p>

                <div class="kfz-hero-actions">
                    <a
                        href="#fahrzeug-abmelden"
                        class="kfz-button kfz-button-primary"
                    >
                        Jetzt Fahrzeug abmelden
                    </a>

                    <a
                        href="<?= $escape($url('/hilfe/')) ?>"
                        class="kfz-button kfz-button-outline"
                    >
                        So funktioniert es
                    </a>
                </div>

                <div class="kfz-hero-trust">
                    <span class="kfz-trust-item">
                        Sicher
                    </span>

                    <span class="kfz-trust-item">
                        Ohne Registrierung
                    </span>

                    <span class="kfz-trust-item">
                        Digital
                    </span>
                </div>

            </div>

        </div>

    </div>

</section>

<?php
require __DIR__ . '/vorgang-starten.php';
?>

<section
    class="kfz-section kfz-section-light"
    aria-labelledby="steps-title"
>
    <div class="container">

        <div class="kfz-section-header kfz-section-header-centered">

            <span class="kfz-section-kicker">
                So funktioniert es
            </span>

            <h2
                id="steps-title"
                class="kfz-section-title"
            >
                In wenigen Schritten zur Abmeldung
            </h2>

        </div>

        <div class="kfz-grid kfz-grid-four kfz-steps">

            <div class="kfz-step">
                <span class="kfz-step-number">1</span>

                <h3>
                    Daten eingeben
                </h3>

                <p>
                    Geben Sie Fahrzeug- und Kontaktdaten ein.
                </p>
            </div>

            <div class="kfz-step">
                <span class="kfz-step-number">2</span>

                <h3>
                    Angaben prüfen
                </h3>

                <p>
                    Prüfen Sie Ihre Angaben vor dem Absenden.
                </p>
            </div>

            <div class="kfz-step">
                <span class="kfz-step-number">3</span>

                <h3>
                    Code bestätigen
                </h3>

                <p>
                    Bestätigen Sie den Vorgang mit dem Zahlencode.
                </p>
            </div>

            <div class="kfz-step">
                <span class="kfz-step-number">4</span>

                <h3>
                    Vorgangsnummer erhalten
                </h3>

                <p>
                    Sie erhalten eine eindeutige Vorgangsnummer.
                </p>
            </div>

        </div>

    </div>
</section>