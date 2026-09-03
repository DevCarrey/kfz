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

<!-- Hero-Bereich -->
<section
    class="hotel-page-hero"
    aria-labelledby="booking-page-title"
>
    <div class="hotel-page-hero-image">

        <img
            src="<?= $escape($asset('/assets/img/hotel.jpg')) ?>"
            alt="Gemütliches Hotelzimmer"
            loading="eager"
        >

        <div class="hotel-page-hero-overlay"></div>

        <div class="container hotel-page-hero-content">

            <span class="hotel-section-kicker hotel-section-kicker-light">
                Ihr Aufenthalt
            </span>

            <h1 id="booking-page-title">
                Zimmer buchen.<br>
                Ankommen.
            </h1>

            <p>
                Planen Sie Ihren Aufenthalt und nehmen Sie direkt
                Kontakt mit uns auf.
            </p>

            <div class="d-flex flex-wrap gap-3">

                <a
                    class="btn btn-hotel-light btn-lg"
                    href="#buchungsformular"
                >
                    Anfrage senden
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

<!-- Einleitung -->
<section
    class="hotel-page-intro py-5"
    aria-labelledby="booking-intro-title"
>
    <div class="container">

        <div class="row justify-content-center text-center">

            <div class="col-lg-8">

                <span class="hotel-section-kicker">
                    Ihre Buchung
                </span>

                <h2
                    id="booking-intro-title"
                    class="hotel-section-title"
                >
                    Einfach anfragen und entspannt planen
                </h2>

                <p class="hotel-intro-text">
                    Sie möchten bei uns übernachten? Senden Sie uns gerne
                    eine unverbindliche Anfrage. Wir melden uns persönlich
                    bei Ihnen und besprechen alle Details zu Ihrem Aufenthalt.
                </p>

            </div>

        </div>

    </div>
</section>

<!-- Buchungsbereich -->
<section
    id="buchungsformular"
    class="hotel-booking-form-section py-5"
    aria-labelledby="booking-form-title"
>
    <div class="container">

        <div class="row g-5 align-items-start">

            <!-- Informationen -->
            <div class="col-lg-5">

                <span class="hotel-section-kicker">
                    Kontakt zur Buchung
                </span>

                <h2
                    id="booking-form-title"
                    class="hotel-featurette-title"
                >
                    Wir freuen uns<br>
                    auf Ihre Anfrage.
                </h2>

                <p class="lead">
                    Füllen Sie das Formular aus oder kontaktieren Sie uns
                    direkt telefonisch.
                </p>

                <p class="text-secondary">
                    Bitte teilen Sie uns nach Möglichkeit Ihren gewünschten
                    Zeitraum und die Anzahl der Personen mit. Wir prüfen Ihre
                    Anfrage und melden uns schnellstmöglich bei Ihnen.
                </p>

                <div class="hotel-contact-box mt-4">

                    <h3>
                        Direkter Kontakt
                    </h3>

                    <p>
                        <strong>Telefon</strong><br>
                        <a href="tel:+49293233116">
                            02932 33116
                        </a>
                    </p>

                    <p class="mb-0">
                        <strong>E-Mail</strong><br>
                        <a href="mailto:info@hotel.de">
                            info@hotel.de
                        </a>
                    </p>

                </div>

            </div>

            <!-- Formular -->
            <div class="col-lg-7">

                <div class="hotel-form-card">

                    <form
                        action="<?= $escape($url('/zimmer-buchen/')) ?>"
                        method="post"
                    >

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label
                                    for="first-name"
                                    class="form-label"
                                >
                                    Vorname
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="first-name"
                                    name="first_name"
                                    autocomplete="given-name"
                                    required
                                >

                            </div>

                            <div class="col-md-6">

                                <label
                                    for="last-name"
                                    class="form-label"
                                >
                                    Nachname
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="last-name"
                                    name="last_name"
                                    autocomplete="family-name"
                                    required
                                >

                            </div>

                            <div class="col-md-6">

                                <label
                                    for="email"
                                    class="form-label"
                                >
                                    E-Mail-Adresse
                                </label>

                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
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
                                    class="form-control"
                                    id="phone"
                                    name="phone"
                                    autocomplete="tel"
                                >

                            </div>

                            <div class="col-md-6">

                                <label
                                    for="arrival"
                                    class="form-label"
                                >
                                    Anreise
                                </label>

                                <input
                                    type="date"
                                    class="form-control"
                                    id="arrival"
                                    name="arrival"
                                    required
                                >

                            </div>

                            <div class="col-md-6">

                                <label
                                    for="departure"
                                    class="form-label"
                                >
                                    Abreise
                                </label>

                                <input
                                    type="date"
                                    class="form-control"
                                    id="departure"
                                    name="departure"
                                    required
                                >

                            </div>

                            <div class="col-md-6">

                                <label
                                    for="guests"
                                    class="form-label"
                                >
                                    Anzahl Personen
                                </label>

                                <select
                                    class="form-select"
                                    id="guests"
                                    name="guests"
                                    required
                                >
                                    <option value="" selected disabled>
                                        Bitte auswählen
                                    </option>

                                    <option value="1">
                                        1 Person
                                    </option>

                                    <option value="2">
                                        2 Personen
                                    </option>

                                    <option value="3">
                                        3 Personen
                                    </option>

                                    <option value="4">
                                        4 Personen
                                    </option>

                                    <option value="5+">
                                        5 oder mehr Personen
                                    </option>

                                </select>

                            </div>

                            <div class="col-md-6">

                                <label
                                    for="room-type"
                                    class="form-label"
                                >
                                    Zimmerwunsch
                                </label>

                                <select
                                    class="form-select"
                                    id="room-type"
                                    name="room_type"
                                >
                                    <option value="" selected>
                                        Keine bestimmte Auswahl
                                    </option>

                                    <option value="einzelzimmer">
                                        Einzelzimmer
                                    </option>

                                    <option value="doppelzimmer">
                                        Doppelzimmer
                                    </option>

                                    <option value="mehrbettzimmer">
                                        Mehrbettzimmer
                                    </option>

                                </select>

                            </div>

                            <div class="col-12">

                                <label
                                    for="message"
                                    class="form-label"
                                >
                                    Nachricht
                                </label>

                                <textarea
                                    class="form-control"
                                    id="message"
                                    name="message"
                                    rows="5"
                                    placeholder="Haben Sie besondere Wünsche oder Fragen?"
                                ></textarea>

                            </div>

                            <div class="col-12">

                                <div class="form-check">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="privacy"
                                        name="privacy"
                                        required
                                    >

                                    <label
                                        class="form-check-label"
                                        for="privacy"
                                    >
                                        Ich habe die
                                        <a
                                            href="<?= $escape($url('/datenschutz/')) ?>"
                                            class="hotel-inline-link"
                                        >
                                            Datenschutzerklärung
                                        </a>
                                        gelesen.
                                    </label>

                                </div>

                            </div>

                            <div class="col-12">

                                <button
                                    type="submit"
                                    class="btn btn-hotel-dark btn-lg"
                                >
                                    Buchungsanfrage senden
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>
</section>

<!-- Ablauf -->
<section
    class="hotel-features-section py-5"
    aria-labelledby="booking-process-title"
>
    <div class="container">

        <div class="text-center mb-5">

            <span class="hotel-section-kicker">
                So funktioniert es
            </span>

            <h2
                id="booking-process-title"
                class="hotel-section-title"
            >
                In drei einfachen Schritten
            </h2>

        </div>

        <div class="row g-4">

            <div class="col-md-4">

                <article class="hotel-feature-card h-100">

                    <div class="hotel-feature-number">
                        01
                    </div>

                    <h3>
                        Anfrage senden
                    </h3>

                    <p>
                        Teilen Sie uns Ihren gewünschten Zeitraum und Ihre
                        persönlichen Daten über das Formular mit.
                    </p>

                </article>

            </div>

            <div class="col-md-4">

                <article class="hotel-feature-card h-100">

                    <div class="hotel-feature-number">
                        02
                    </div>

                    <h3>
                        Rückmeldung erhalten
                    </h3>

                    <p>
                        Wir prüfen Ihre Anfrage und melden uns persönlich
                        bei Ihnen mit weiteren Informationen.
                    </p>

                </article>

            </div>

            <div class="col-md-4">

                <article class="hotel-feature-card h-100">

                    <div class="hotel-feature-number">
                        03
                    </div>

                    <h3>
                        Aufenthalt genießen
                    </h3>

                    <p>
                        Nach der Bestätigung freuen wir uns darauf,
                        Sie bei uns willkommen zu heißen.
                    </p>

                </article>

            </div>

        </div>

    </div>
</section>

<!-- Abschluss -->
<section
    class="hotel-atmosphere-section py-5"
    aria-labelledby="booking-final-title"
>
    <div class="container text-center">

        <span class="hotel-section-kicker hotel-section-kicker-light">
            Fragen zur Buchung?
        </span>

        <h2
            id="booking-final-title"
            class="hotel-featurette-title text-white"
        >
            Wir helfen Ihnen<br>
            gerne weiter.
        </h2>

        <p class="lead text-white">
            Rufen Sie uns direkt an oder schreiben Sie uns eine Nachricht.
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
                href="<?= $escape($url('/kontakt/')) ?>"
            >
                Kontaktseite öffnen
            </a>

        </div>

    </div>
</section>