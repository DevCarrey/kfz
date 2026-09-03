<?php
declare(strict_types=1);

/**
 * Kfz Digital – Startseite
 *
 * Der Header und Footer werden durch index.php geladen.
 * Deshalb wird der Header hier nicht erneut eingebunden.
 */

$appPrefix = rtrim(
    (string)($GLOBALS['appPrefix'] ?? ''),
    '/'
);

/**
 * Erstellt interne URLs inklusive Projekt-Prefix.
 */
$url = static function (string $path) use ($appPrefix): string {
    $path = '/' . ltrim($path, '/');

    return $appPrefix . ($path === '/' ? '/' : $path);
};

/**
 * Erstellt URLs zu Dateien im public-Verzeichnis.
 */
$asset = static function (string $path) use ($appPrefix): string {
    return $appPrefix . '/public/' . ltrim($path, '/');
};

/**
 * Escaping für HTML-Ausgaben.
 */
$escape = static function (string $value): string {
    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
};
?>

<!-- =====================================================
     Hero-Bereich
     ===================================================== -->

<section class="kfz-hero">

    <div class="container">

        <div class="kfz-hero-layout">

            <div class="kfz-hero-content">

                <span class="kfz-section-kicker">
                    Fahrzeug online abmelden
                </span>

                <h1 class="kfz-hero-title">
                    Fahrzeug abmelden.
                    Einfach digital.
                </h1>

                <p class="kfz-hero-text">
                    Mit Kfz Digital bereiten Sie die Abmeldung Ihres Fahrzeugs
                    übersichtlich, sicher und bequem online vor.
                </p>

                <div class="kfz-hero-actions">

                    <a
                        href="<?= $escape($url('/vorgang-starten/')) ?>"
                        class="kfz-button kfz-button-primary"
                    >
                        <svg
                            class="kfz-button-icon"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M12 5v14M5 12h14" />
                        </svg>

                        Fahrzeug abmelden
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

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6l-7-3Z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>

                        Sicher

                    </span>

                    <span class="kfz-trust-item">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 2" />
                        </svg>

                        Zeit sparen

                    </span>

                    <span class="kfz-trust-item">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M4 5h16v12H8l-4 4V5Z" />
                            <path d="M8 9h8M8 13h5" />
                        </svg>

                        Digital

                    </span>

                </div>

            </div>


            <!-- Schnelleinstieg -->

            <div class="kfz-hero-card">

                <div class="kfz-hero-card-header">

                    <span class="kfz-hero-card-icon">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M5 16h14l-1.5-6H6.5L5 16Z" />
                            <path d="M4 16h16v4H4v-4ZM7 20v1m10-1v1M7 10l1.5-3h7L17 10" />
                            <circle cx="8" cy="16" r="1" />
                            <circle cx="16" cy="16" r="1" />
                        </svg>

                    </span>

                    <div>

                        <strong>
                            Fahrzeug abmelden
                        </strong>

                        <small>
                            Schnell und übersichtlich beginnen
                        </small>

                    </div>

                </div>


                <form
                    action="<?= $escape($url('/vorgang-starten/')) ?>"
                    method="get"
                    class="kfz-quick-form"
                >

                    <div class="kfz-form-group">

                        <label
                            for="vehicle-process"
                            class="kfz-form-label"
                        >
                            Fahrzeugabmeldung starten
                        </label>

                        <select
                            id="vehicle-process"
                            name="vorgang"
                            class="kfz-form-select"
                            aria-readonly="true"
                        >

                            <option value="abmeldung" selected>
                                Fahrzeug abmelden
                            </option>

                        </select>

                    </div>


                    <div class="kfz-form-group">

                        <label
                            for="license-plate"
                            class="kfz-form-label"
                        >
                            Kennzeichen
                        </label>

                        <input
                            type="text"
                            id="license-plate"
                            name="kennzeichen"
                            class="kfz-form-control"
                            placeholder="z. B. MK-KD 123"
                            maxlength="15"
                            autocomplete="off"
                        >

                    </div>


                    <button
                        type="submit"
                        class="kfz-button kfz-button-primary kfz-button-full"
                    >
                        Abmeldung beginnen

                        <svg
                            class="kfz-button-icon"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M5 12h14M13 6l6 6-6 6" />
                        </svg>

                    </button>

                    <p class="kfz-form-notice">
                        Ihre Daten werden sicher übertragen.
                    </p>

                </form>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     Leistungen
     ===================================================== -->

<section
    class="kfz-section kfz-section-light"
    aria-labelledby="services-title"
>

    <div class="container">

        <div class="kfz-section-header kfz-section-header-centered">

            <span class="kfz-section-kicker">
                Unsere Leistung
            </span>

            <h2
                id="services-title"
                class="kfz-section-title"
            >
                Fahrzeug digital abmelden
            </h2>

            <p class="kfz-section-text">
                Bereiten Sie die Abmeldung Ihres Fahrzeugs direkt digital vor.
            </p>

        </div>


        <div class="kfz-grid">


            <!-- Fahrzeug abmelden -->

            <a
                href="<?= $escape($url('/vorgang-starten/?vorgang=abmeldung')) ?>"
                class="kfz-card kfz-service-card"
            >

                <span class="kfz-card-icon kfz-card-icon-orange">

                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="M5 16h14l-1.5-6H6.5L5 16Z" />
                        <path d="M4 16h16v4H4v-4ZM7 20v1m10-1v1M7 10l1.5-3h7L17 10" />
                        <path d="M9 13h6" />
                    </svg>

                </span>

                <h3 class="kfz-card-title">
                    Fahrzeug abmelden
                </h3>

                <p class="kfz-card-text">
                    Beenden Sie die Zulassung Ihres Fahrzeugs schnell und digital.
                </p>

                <span class="kfz-service-link">
                    Vorgang starten
                    <span aria-hidden="true">→</span>
                </span>

            </a>


        </div>

    </div>

</section>


<!-- =====================================================
     Vorteile
     ===================================================== -->

<section
    class="kfz-section kfz-benefits-section"
    aria-labelledby="benefits-title"
>

    <div class="container">

        <div class="kfz-two-column">

            <div>

                <span class="kfz-section-kicker">
                    Warum Kfz Digital?
                </span>

                <h2
                    id="benefits-title"
                    class="kfz-section-title"
                >
                    Weniger Aufwand für Ihre Fahrzeugverwaltung.
                </h2>

                <p class="kfz-section-text">
                    Kfz Digital führt Sie Schritt für Schritt durch Ihre
                    Fahrzeugvorgänge und sorgt für mehr Übersicht.
                </p>

                <a
                    href="<?= $escape($url('/ueber-kfz-digital/')) ?>"
                    class="kfz-button kfz-button-outline kfz-button-spaced"
                >
                    Mehr über Kfz Digital
                </a>

            </div>


            <div class="kfz-benefit-list">

                <div class="kfz-benefit-item">

                    <span class="kfz-benefit-number">
                        01
                    </span>

                    <div>

                        <h3>
                            Alles an einem Ort
                        </h3>

                        <p>
                            Verwalten Sie Fahrzeuge, Dokumente und Anträge
                            übersichtlich in Ihrem persönlichen Bereich.
                        </p>

                    </div>

                </div>


                <div class="kfz-benefit-item">

                    <span class="kfz-benefit-number">
                        02
                    </span>

                    <div>

                        <h3>
                            Einfach verständlich
                        </h3>

                        <p>
                            Klare Formulare und hilfreiche Hinweise begleiten
                            Sie durch jeden einzelnen Schritt.
                        </p>

                    </div>

                </div>


                <div class="kfz-benefit-item">

                    <span class="kfz-benefit-number">
                        03
                    </span>

                    <div>

                        <h3>
                            Status jederzeit im Blick
                        </h3>

                        <p>
                            Prüfen Sie den aktuellen Bearbeitungsstand Ihrer
                            Vorgänge online.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     Ablauf
     ===================================================== -->

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
                In vier einfachen Schritten zur Abmeldung
            </h2>

            <p class="kfz-section-text">
                Starten Sie Ihre Abmeldung und behalten Sie jederzeit den Überblick.
            </p>

        </div>


        <div class="kfz-grid kfz-grid-four kfz-steps">

            <div class="kfz-step">

                <span class="kfz-step-number">
                    1
                </span>

                <h3>
                    Fahrzeug auswählen
                </h3>

                <p>
                    Wählen Sie das Fahrzeug aus, das Sie abmelden möchten.
                </p>

            </div>


            <div class="kfz-step">

                <span class="kfz-step-number">
                    2
                </span>

                <h3>
                    Daten eingeben
                </h3>

                <p>
                    Geben Sie die erforderlichen Daten zu Ihnen und Ihrem
                    Fahrzeug ein.
                </p>

            </div>


            <div class="kfz-step">

                <span class="kfz-step-number">
                    3
                </span>

                <h3>
                    Dokumente bereitstellen
                </h3>

                <p>
                    Laden Sie die erforderlichen Unterlagen hoch und prüfen
                    Sie Ihre Angaben.
                </p>

            </div>


            <div class="kfz-step">

                <span class="kfz-step-number">
                    4
                </span>

                <h3>
                    Status verfolgen
                </h3>

                <p>
                    Behalten Sie den Bearbeitungsstand Ihres Vorgangs jederzeit
                    im Blick.
                </p>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     Sicherheit
     ===================================================== -->

<section class="kfz-section kfz-trust-section">

    <div class="container">

        <div class="kfz-trust-box">

            <div class="kfz-trust-box-icon">

                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6l-7-3Z" />
                    <path d="m9 12 2 2 4-4" />
                </svg>

            </div>


            <div>

                <h2>
                    Sicherheit und Datenschutz im Mittelpunkt.
                </h2>

                <p>
                    Kfz Digital wurde für eine sichere und übersichtliche
                    Verwaltung von Fahrzeugabmeldungen entwickelt.
                </p>

            </div>


            <a
                href="<?= $escape($url('/datenschutz/')) ?>"
                class="kfz-button kfz-button-light"
            >
                Datenschutz
            </a>

        </div>

    </div>

</section>


<!-- =====================================================
     Call-to-Action
     ===================================================== -->

<section
    class="kfz-section kfz-cta-section"
    aria-labelledby="cta-title"
>

    <div class="container">

        <div class="kfz-cta-box">

            <span class="kfz-section-kicker">
                Jetzt loslegen
            </span>

            <h2
                id="cta-title"
                class="kfz-section-title"
            >
                Bereit für Ihre digitale Fahrzeugabmeldung?
            </h2>

            <p class="kfz-section-text">
                Starten Sie jetzt die Abmeldung Ihres Fahrzeugs mit Kfz Digital.
            </p>

            <a
                href="<?= $escape($url('/vorgang-starten/')) ?>"
                class="kfz-button kfz-button-primary"
            >
                Fahrzeug abmelden

                <svg
                    class="kfz-button-icon"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path d="M5 12h14M13 6l6 6-6 6" />
                </svg>

            </a>

        </div>

    </div>

</section>
