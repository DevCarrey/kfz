<?php
declare(strict_types=1);

$appPrefix = rtrim((string)($GLOBALS['appPrefix'] ?? ''), '/');

/**
 * Erstellt interne URLs.
 */
$url = static function (string $path) use ($appPrefix): string {
    return $appPrefix . '/' . ltrim($path, '/');
};

/**
 * Erstellt Asset-URLs.
 */
$asset = static function (string $path) use ($appPrefix): string {
    return $appPrefix . '/public/' . ltrim($path, '/');
};

/**
 * Sicheres Escaping.
 */
$escape = static function (string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
};
?>

<!-- Kontakt Hero -->
<section
    class="hotel-page-hero"
    aria-labelledby="contact-page-title"
>
    <div class="hotel-page-hero-image">

        <img
            src="<?= $escape($asset('/assets/img/Einfahrt.jpg')) ?>"
            alt="Außenansicht des Hotels"
            loading="eager"
        >

        <div class="hotel-page-hero-overlay"></div>

        <div class="container hotel-page-hero-content">

            <span class="hotel-section-kicker hotel-section-kicker-light">
                Kontakt
            </span>

            <h1 id="contact-page-title">
                Wir freuen uns<br>
                von Ihnen zu hören.
            </h1>

            <p>
                Haben Sie Fragen zu unseren Zimmern, zur Gastronomie
                oder zu Ihrem Aufenthalt? Schreiben Sie uns gerne.
            </p>

            <div class="d-flex flex-wrap gap-3">

                <a
                    class="btn btn-hotel-light btn-lg"
                    href="#kontaktformular"
                >
                    Nachricht schreiben
                </a>

                <a
                    class="btn btn-hotel-outline-light btn-lg"
                    href="tel:+49293233116"
                >
                    Jetzt anrufen
                </a>

            </div>

        </div>

    </div>
</section>

<!-- Kontaktinformationen -->
<section
    class="hotel-contact-section py-5"
    aria-labelledby="contact-info-title"
>
    <div class="container">

        <div class="text-center mb-5">

            <span class="hotel-section-kicker">
                Ihre Ansprechpartner
            </span>

            <h2
                id="contact-info-title"
                class="hotel-section-title"
            >
                Kontaktieren Sie uns
            </h2>

            <p class="hotel-section-text">
                Wir helfen Ihnen gerne persönlich weiter.
            </p>

        </div>

        <div class="row g-4">

            <div class="col-md-4">

                <article class="hotel-contact-card h-100">

                    <div
                        class="hotel-contact-icon"
                        aria-hidden="true"
                    >
                        📍
                    </div>

                    <h3>
                        Adresse
                    </h3>

                    <p>
                        Stadt Hotel<br>
                        Specksloh 12<br>
                        59757 Arnsberg-Voßwinkel
                    </p>

                    <a
                        href="https://www.google.com/maps/dir//Specksloh+12,+59757+Arnsberg"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hotel-card-link"
                    >
                        Route anzeigen
                        <span aria-hidden="true">→</span>
                    </a>

                </article>

            </div>

            <div class="col-md-4">

                <article class="hotel-contact-card h-100">

                    <div
                        class="hotel-contact-icon"
                        aria-hidden="true"
                    >
                        📞
                    </div>

                    <h3>
                        Telefon
                    </h3>

                    <p>
                        Sie erreichen uns telefonisch
                        während unserer Öffnungszeiten.
                    </p>

                    <a
                        href="tel:+49293233116"
                        class="hotel-card-link"
                    >
                        02932 33116
                        <span aria-hidden="true">→</span>
                    </a>

                </article>

            </div>

            <div class="col-md-4">

                <article class="hotel-contact-card h-100">

                    <div
                        class="hotel-contact-icon"
                        aria-hidden="true"
                    >
                        ✉️
                    </div>

                    <h3>
                        E-Mail
                    </h3>

                    <p>
                        Schreiben Sie uns gerne eine Nachricht.
                        Wir melden uns schnellstmöglich zurück.
                    </p>

                    <a
                        href="mailto:info@hotel.de"
                        class="hotel-card-link"
                    >
                        E-Mail schreiben
                        <span aria-hidden="true">→</span>
                    </a>

                </article>

            </div>

        </div>

    </div>
</section>

<!-- Kontaktformular -->
<section
    id="kontaktformular"
    class="hotel-contact-form-section py-5"
    aria-labelledby="form-title"
>
    <div class="container">

        <div class="row align-items-start g-5">

            <!-- Informationen -->
            <div class="col-lg-5">

                <span class="hotel-section-kicker">
                    Schreiben Sie uns
                </span>

                <h2
                    id="form-title"
                    class="hotel-featurette-title"
                >
                    Wie können wir<br>
                    Ihnen helfen?
                </h2>

                <p class="lead">
                    Nutzen Sie unser Kontaktformular für Fragen,
                    Reservierungen oder besondere Wünsche.
                </p>

                <p class="text-secondary">
                    Bitte füllen Sie die mit einem Sternchen markierten
                    Felder aus. Wir bearbeiten Ihre Anfrage so schnell
                    wie möglich.
                </p>

                <div class="hotel-opening-box mt-4">

                    <h3>
                        Erreichbarkeit
                    </h3>

                    <ul>
                        <li>
                            Montag–Freitag: 07:00–18:00 Uhr
                        </li>

                        <li>
                            Samstag: 07:00–13:00 Uhr
                        </li>

                        <li>
                            Sonntag: Geschlossen
                        </li>
                    </ul>

                </div>

            </div>

            <!-- Formular -->
            <div class="col-lg-7">

                <div class="hotel-form-card">

                    <form
                        action="<?= $escape($url('/kontakt/')) ?>"
                        method="post"
                    >

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label
                                    for="first-name"
                                    class="form-label"
                                >
                                    Vorname *
                                </label>

                                <input
                                    type="text"
                                    id="first-name"
                                    name="first_name"
                                    class="form-control"
                                    autocomplete="given-name"
                                    required
                                >

                            </div>

                            <div class="col-md-6">

                                <label
                                    for="last-name"
                                    class="form-label"
                                >
                                    Nachname *
                                </label>

                                <input
                                    type="text"
                                    id="last-name"
                                    name="last_name"
                                    class="form-control"
                                    autocomplete="family-name"
                                    required
                                >

                            </div>

                            <div class="col-md-6">

                                <label
                                    for="email"
                                    class="form-label"
                                >
                                    E-Mail-Adresse *
                                </label>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control"
                                    autocomplete="email"
                                    required
                                >

                            </div>

                            <div class="col-md-6">

                                <label
                                    for="phone"
                                    class="form-label"
                                >
                                    Telefonnummer
                                </label>

                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    class="form-control"
                                    autocomplete="tel"
                                >

                            </div>

                            <div class="col-12">

                                <label
                                    for="subject"
                                    class="form-label"
                                >
                                    Betreff *
                                </label>

                                <select
                                    id="subject"
                                    name="subject"
                                    class="form-select"
                                    required
                                >
                                    <option
                                        value=""
                                        selected
                                        disabled
                                    >
                                        Bitte auswählen
                                    </option>

                                    <option value="zimmer">
                                        Zimmer buchen
                                    </option>

                                    <option value="gastronomie">
                                        Gastronomie
                                    </option>

                                    <option value="allgemein">
                                        Allgemeine Anfrage
                                    </option>

                                    <option value="sonstiges">
                                        Sonstiges
                                    </option>

                                </select>

                            </div>

                            <div class="col-12">

                                <label
                                    for="message"
                                    class="form-label"
                                >
                                    Ihre Nachricht *
                                </label>

                                <textarea
                                    id="message"
                                    name="message"
                                    class="form-control"
                                    rows="6"
                                    placeholder="Wie können wir Ihnen helfen?"
                                    required
                                ></textarea>

                            </div>

                            <div class="col-12">

                                <div class="form-check">

                                    <input
                                        type="checkbox"
                                        id="privacy"
                                        name="privacy"
                                        class="form-check-input"
                                        required
                                    >

                                    <label
                                        for="privacy"
                                        class="form-check-label"
                                    >
                                        Ich habe die
                                        <a
                                            href="<?= $escape($url('/datenschutz/')) ?>"
                                            class="hotel-inline-link"
                                        >
                                            Datenschutzerklärung
                                        </a>
                                        gelesen. *
                                    </label>

                                </div>

                            </div>

                            <div class="col-12">

                                <button
                                    type="submit"
                                    class="btn btn-hotel-dark btn-lg"
                                >
                                    Nachricht senden
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>
</section>

<!-- Abschluss CTA -->
<section
    class="hotel-atmosphere-section py-5"
    aria-labelledby="contact-final-title"
>
    <div class="container text-center">

        <span class="hotel-section-kicker hotel-section-kicker-light">
            Persönlicher Kontakt
        </span>

        <h2
            id="contact-final-title"
            class="hotel-featurette-title text-white"
        >
            Wir sind gerne<br>
            für Sie da.
        </h2>

        <p class="lead text-white">
            Ob telefonisch, per E-Mail oder persönlich vor Ort.
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">

            <a
                class="btn btn-hotel-light btn-lg"
                href="tel:+49293233116"
            >
                02932 33116
            </a>

            <a
                class="btn btn-hotel-outline-light btn-lg"
                href="<?= $escape($url('/zimmer-buchen/')) ?>"
            >
                Zimmer buchen
            </a>

        </div>

    </div>
</section>