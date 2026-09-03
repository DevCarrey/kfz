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

$highlights = [
    [
        'icon'  => '🍽️',
        'title' => 'Tolles Restaurant',
        'text'  => 'Frühstück und Abendessen in angenehmer Atmosphäre.',
    ],
    [
        'icon'  => '🛏️',
        'title' => 'Schöne Zimmer',
        'text'  => 'Gemütliche und komfortable Zimmer für Geschäftsreisende und Familien.',
    ],
    [
        'icon'  => '📍',
        'title' => 'Zentrale Lage',
        'text'  => 'Stadt und Land vereint – mitten in der Waldstadt Iserlohn.',
    ],
];

$statistics = [
    [
        'number' => '38',
        'label'  => 'Zimmer',
    ],
    [
        'number' => '15',
        'label'  => 'Mitarbeiter',
    ],
    [
        'number' => '10',
        'label'  => 'Services',
    ],
    [
        'number' => '24/7',
        'label'  => 'Bar & Service',
    ],
];

$hotelServices = [
    [
        'title' => 'WLAN',
        'text'  => 'Das Hotel bietet Ihnen auf jeder Etage und in den öffentlichen Räumlichkeiten kostenfreies WLAN. Das Passwort erhalten Sie an der Rezeption.',
        'icon'  => '📶',
    ],
    [
        'title' => 'Zirbelstube',
        'text'  => 'In unserer rustikalen Zirbelstube treffen historische Materialien auf eine gemütliche Atmosphäre. Die teilweise über 200 Jahre alten Materialien machen diesen Ort besonders.',
        'icon'  => '🍺',
    ],
    [
        'title' => 'Wasserflasche',
        'text'  => 'Auf jedem Zimmer befindet sich eine kostenfreie Flasche Wasser. Weitere Getränke erhalten Sie an unserer rund um die Uhr geöffneten Bar.',
        'icon'  => '💧',
    ],
    [
        'title' => 'Parkgarage',
        'text'  => 'Ihr Fahrzeug können Sie sicher und trocken in unserer Parkgarage abstellen. Die Nutzung kostet 9,90 Euro pro Auto und Tag.',
        'icon'  => '🚗',
    ],
];
?>

<!-- Hero-Bereich -->
<section
    class="hotel-page-hero"
    aria-labelledby="about-page-title"
>
    <div class="hotel-page-hero-image">

        <img
            src="<?= $escape($asset('/assets/img/Einfahrt.jpg')) ?>"
            alt="Außenansicht des Stadthotels Iserlohn"
            loading="eager"
        >

        <div class="hotel-page-hero-overlay"></div>

        <div class="container hotel-page-hero-content">

            <span class="hotel-section-kicker hotel-section-kicker-light">
                Über unser Hotel
            </span>

            <h1 id="about-page-title">
                Das Stadt Hotel.<br>
                Im Herzen der Waldstadt.
            </h1>

            <p>
                Ein modernes, innerstädtisches Hotel mit persönlicher
                Atmosphäre, gemütlichen Zimmern und gastronomischem Angebot.
            </p>

            <div class="d-flex flex-wrap gap-3">

                <a
                    class="btn btn-hotel-light btn-lg"
                    href="<?= $escape($url('/zimmer-buchen/')) ?>"
                >
                    Zimmer buchen
                </a>

                <a
                    class="btn btn-hotel-outline-light btn-lg"
                    href="#hotel-ueberblick"
                >
                    Hotel entdecken
                </a>

            </div>

        </div>

    </div>
</section>

<!-- Einleitung -->
<section
    class="hotel-page-intro py-5"
    aria-labelledby="about-intro-title"
>
    <div class="container">

        <div class="row align-items-center g-5">

            <div class="col-lg-7">

                <span class="hotel-section-kicker">
                    Willkommen im Stadt Hotel
                </span>

                <h2
                    id="about-intro-title"
                    class="hotel-section-title"
                >
                    Ein Ort zum Ankommen und Wohlfühlen
                </h2>

                <p class="lead">
                    Das Stadt Hotel Iserlohn liegt im Herzen der Waldstadt
                    und steht seinen Gästen als modernes, innerstädtisches
                    Hotel zur Verfügung.
                </p>

                <p class="text-secondary">
                    Mit insgesamt 38 Einzel- und Doppelzimmern verfügt unser
                    Stadthotel über individuell gestaltete Zimmer und
                    Raumeinheiten, die sowohl Geschäftsreisende als auch
                    Familien ansprechen.
                </p>

                <p class="text-secondary mb-0">
                    Seit April 2019 befindet sich das Stadthotel im Besitz
                    von Herrn Ilir Mulaku. Seitdem wurde das Haus weiter
                    entwickelt und durch neue gastronomische Angebote ergänzt.
                </p>

            </div>

            <div class="col-lg-5">

                <div class="hotel-room-image-wrapper">

                    <img
                        src="<?= $escape($asset('/assets/img/hotel.jpg')) ?>"
                        class="hotel-room-image"
                        alt="Gemütlicher Bereich im Stadt Hotel"
                        loading="lazy"
                    >

                </div>

            </div>

        </div>

    </div>
</section>

<!-- Highlights -->
<section
    class="hotel-features-section py-5"
    aria-labelledby="highlights-title"
>
    <div class="container">

        <div class="text-center mb-5">

            <span class="hotel-section-kicker">
                Das erwartet Sie
            </span>

            <h2
                id="highlights-title"
                class="hotel-section-title"
            >
                Hotel, Gastronomie und Service
            </h2>

        </div>

        <div class="row g-4">

            <?php foreach ($highlights as $highlight): ?>

                <div class="col-md-4">

                    <article class="hotel-feature-card h-100">

                        <div
                            class="hotel-feature-icon"
                            aria-hidden="true"
                        >
                            <?= $escape($highlight['icon']) ?>
                        </div>

                        <h3>
                            <?= $escape($highlight['title']) ?>
                        </h3>

                        <p>
                            <?= $escape($highlight['text']) ?>
                        </p>

                    </article>

                </div>

            <?php endforeach; ?>

        </div>

    </div>
</section>

<!-- Statistik -->
<section
    id="hotel-ueberblick"
    class="hotel-statistics-section py-5"
    aria-labelledby="statistics-title"
>
    <div class="container">

        <div class="text-center mb-5">

            <span class="hotel-section-kicker">
                Das Stadt Hotel im Überblick
            </span>

            <h2
                id="statistics-title"
                class="hotel-section-title"
            >
                Zahlen, die unser Haus beschreiben
            </h2>

        </div>

        <div class="row g-4 justify-content-center">

            <?php foreach ($statistics as $statistic): ?>

                <div class="col-6 col-lg-3">

                    <div class="hotel-stat-card text-center">

                        <div class="hotel-stat-number">
                            <?= $escape($statistic['number']) ?>
                        </div>

                        <div class="hotel-stat-label">
                            <?= $escape($statistic['label']) ?>
                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>
</section>

<!-- Geschichte -->
<section
    class="hotel-featurette hotel-featurette-dark py-5"
    aria-labelledby="history-title"
>
    <div class="container">

        <div class="row align-items-center g-5">

            <div class="col-lg-5">

                <div class="hotel-featurette-image-wrapper">

                    <img
                        src="<?= $escape($asset('/assets/img/TeamVogt.jpg')) ?>"
                        class="hotel-featurette-image"
                        alt="Team des Stadt Hotels"
                        loading="lazy"
                    >

                </div>

            </div>

            <div class="col-lg-7">

                <span class="hotel-section-kicker hotel-section-kicker-light">
                    Unsere Geschichte
                </span>

                <h2
                    id="history-title"
                    class="hotel-featurette-title text-white"
                >
                    Modernes Hotel<br>
                    mit persönlicher Note.
                </h2>

                <p class="lead text-white">
                    Seit April 2019 wird das Stadthotel von Herrn Ilir Mulaku
                    geführt und kontinuierlich weiterentwickelt.
                </p>

                <p class="text-white-50">
                    Mit dem neu eröffneten Frühstücksraum
                    „breakfast by Bora“ und dem Restaurant
                    „Trattoria Garibaldi“ bietet das Hotel seinen Gästen
                    Möglichkeiten für einen genussvollen Start in den Tag
                    und einen entspannten Abend.
                </p>

                <p class="text-white-50 mb-0">
                    Wir freuen uns darauf, Sie bei uns willkommen zu heißen.
                </p>

            </div>

        </div>

    </div>
</section>

<!-- Gastronomie -->
<section
    class="hotel-page-intro py-5"
    aria-labelledby="restaurant-title"
>
    <div class="container">

        <div class="row align-items-center g-5">

            <div class="col-lg-7">

                <span class="hotel-section-kicker">
                    Frühstück und Abendessen
                </span>

                <h2
                    id="restaurant-title"
                    class="hotel-featurette-title"
                >
                    Frühstück bei Bora.<br>
                    Abendessen bei Garibaldi.
                </h2>

                <p class="lead">
                    Unser Frühstücksraum und unser Restaurant laden zum
                    Genießen und Verweilen ein.
                </p>

                <p class="text-secondary">
                    Beginnen Sie den Tag mit einem Frühstück in angenehmer
                    Atmosphäre oder lassen Sie den Abend bei einem guten Essen
                    und ausgewählten Getränken ausklingen.
                </p>

                <a
                    class="btn btn-hotel-dark mt-3"
                    href="<?= $escape($url('/gastronomie/')) ?>"
                >
                    Gastronomie ansehen
                </a>

            </div>

            <div class="col-lg-5">

                <div class="hotel-room-image-wrapper">

                    <img
                        src="<?= $escape($asset('/assets/img/gastronomie.jpg')) ?>"
                        class="hotel-room-image"
                        alt="Restaurantbereich des Stadt Hotels"
                        loading="lazy"
                    >

                </div>

            </div>

        </div>

    </div>
</section>

<!-- Services -->
<section
    class="hotel-features-section py-5"
    aria-labelledby="services-title"
>
    <div class="container">

        <div class="text-center mb-5">

            <span class="hotel-section-kicker">
                Komfort und Service
            </span>

            <h2
                id="services-title"
                class="hotel-section-title"
            >
                Alles für einen angenehmen Aufenthalt
            </h2>

            <p class="hotel-section-text">
                Praktische Services und kleine Details, die Ihren Aufenthalt
                noch angenehmer machen.
            </p>

        </div>

        <div class="row g-4">

            <?php foreach ($hotelServices as $service): ?>

                <div class="col-md-6">

                    <article class="hotel-service-card h-100">

                        <div
                            class="hotel-service-icon"
                            aria-hidden="true"
                        >
                            <?= $escape($service['icon']) ?>
                        </div>

                        <div>

                            <h3>
                                <?= $escape($service['title']) ?>
                            </h3>

                            <p>
                                <?= $escape($service['text']) ?>
                            </p>

                        </div>

                    </article>

                </div>

            <?php endforeach; ?>

        </div>

    </div>
</section>

<!-- Abschluss CTA -->
<section
    class="hotel-booking-section py-5"
    aria-labelledby="about-booking-title"
>
    <div class="container">

        <div class="hotel-booking-card">

            <div class="row align-items-center g-4">

                <div class="col-lg-8">

                    <span class="hotel-section-kicker">
                        Herzlich willkommen
                    </span>

                    <h2 id="about-booking-title">
                        Wir freuen uns auf Ihren Besuch.
                    </h2>

                    <p class="mb-0">
                        Entdecken Sie das Stadt Hotel im Herzen der Waldstadt
                        und planen Sie jetzt Ihren Aufenthalt.
                    </p>

                </div>

                <div class="col-lg-4 text-lg-end">

                    <a
                        class="btn btn-hotel-dark btn-lg"
                        href="<?= $escape($url('/zimmer-buchen/')) ?>"
                    >
                        Zimmer buchen
                    </a>

                </div>

            </div>

        </div>

    </div>
</section>