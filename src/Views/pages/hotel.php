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
 * Erstellt URLs zu Dateien im public-Verzeichnis.
 */
$asset = static function (string $path) use ($appPrefix): string {
    return $appPrefix . '/public/' . ltrim($path, '/');
};

/**
 * Sicheres Escaping für HTML.
 */
$escape = static function (string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
};

$hotelFeatures = [
    [
        'icon'  => '🛏️',
        'title' => 'Komfortable Zimmer',
        'text'  => 'Unsere Zimmer bieten Ihnen einen angenehmen Rückzugsort für erholsame Nächte.',
    ],
    [
        'icon'  => '☕',
        'title' => 'Angenehme Atmosphäre',
        'text'  => 'Ankommen, abschalten und sich vom ersten Moment an wohlfühlen.',
    ],
    [
        'icon'  => '📍',
        'title' => 'Gute Lage',
        'text'  => 'Unser Hotel ist der ideale Ausgangspunkt für Ihre Reise, Ihren Besuch oder Ihre geschäftlichen Termine.',
    ],
];

$roomDetails = [
    'Check-in'  => 'Nach Absprache',
    'Check-out' => 'Nach Absprache',
    'Kontakt'   => 'Telefonisch oder per E-Mail',
];
?>

<!-- Hotel Hero -->
<section
    class="hotel-page-hero"
    aria-labelledby="hotel-page-title"
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

            <h1 id="hotel-page-title">
                Ankommen und<br>
                wohlfühlen.
            </h1>

            <p>
                Genießen Sie eine angenehme Übernachtung
                in persönlicher Atmosphäre.
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
                    href="#zimmer"
                >
                    Mehr erfahren
                </a>

            </div>

        </div>

    </div>
</section>

<!-- Einleitung -->
<section
    class="hotel-page-intro py-5"
    aria-labelledby="intro-title"
>
    <div class="container">

        <div class="row justify-content-center text-center">

            <div class="col-lg-8">

                <span class="hotel-section-kicker">
                    Willkommen im Hotel
                </span>

                <h2
                    id="intro-title"
                    class="hotel-section-title"
                >
                    Ihr Rückzugsort für entspannte Tage
                </h2>

                <p class="hotel-intro-text">
                    Ob Sie geschäftlich unterwegs sind, Freunde und Familie
                    besuchen oder einfach eine erholsame Auszeit genießen
                    möchten – bei uns finden Sie einen angenehmen Ort zum
                    Übernachten und Wohlfühlen.
                </p>

            </div>

        </div>

    </div>
</section>

<!-- Vorteile -->
<section
    class="hotel-features-section py-5"
    aria-labelledby="features-title"
>
    <div class="container">

        <div class="text-center mb-5">

            <span class="hotel-section-kicker">
                Ihre Vorteile
            </span>

            <h2
                id="features-title"
                class="hotel-section-title"
            >
                Darauf können Sie sich freuen
            </h2>

        </div>

        <div class="row g-4">

            <?php foreach ($hotelFeatures as $feature): ?>

                <div class="col-md-4">

                    <article class="hotel-feature-card h-100">

                        <div
                            class="hotel-feature-icon"
                            aria-hidden="true"
                        >
                            <?= $escape($feature['icon']) ?>
                        </div>

                        <h3>
                            <?= $escape($feature['title']) ?>
                        </h3>

                        <p>
                            <?= $escape($feature['text']) ?>
                        </p>

                    </article>

                </div>

            <?php endforeach; ?>

        </div>

    </div>
</section>

<!-- Zimmerbereich -->
<section
    id="zimmer"
    class="hotel-room-section py-5"
    aria-labelledby="room-title"
>
    <div class="container">

        <div class="row align-items-center g-5">

            <div class="col-lg-6">

                <div class="hotel-room-image-wrapper">

                    <img
                        src="<?= $escape($asset('/assets/img/Zimmer2.jpg')) ?>"
                        class="hotel-room-image"
                        alt="Helles und gemütliches Hotelzimmer"
                        loading="lazy"
                    >

                </div>

            </div>

            <div class="col-lg-6">

                <span class="hotel-section-kicker">
                    Unsere Zimmer
                </span>

                <h2
                    id="room-title"
                    class="hotel-featurette-title"
                >
                    Komfort, Ruhe und<br>
                    persönlicher Service.
                </h2>

                <p class="lead">
                    Unsere Zimmer sind der ideale Ort, um nach einem langen
                    Tag zur Ruhe zu kommen.
                </p>

                <p class="text-secondary">
                    Wir legen Wert auf eine angenehme Atmosphäre, unkomplizierte
                    Abläufe und einen persönlichen Umgang mit unseren Gästen.
                </p>

                <div class="hotel-room-details">

                    <?php foreach ($roomDetails as $label => $value): ?>

                        <div class="hotel-room-detail">

                            <span class="hotel-room-detail-label">
                                <?= $escape($label) ?>
                            </span>

                            <span class="hotel-room-detail-value">
                                <?= $escape($value) ?>
                            </span>

                        </div>

                    <?php endforeach; ?>

                </div>

                <a
                    class="btn btn-hotel-dark mt-4"
                    href="<?= $escape($url('/zimmer-buchen/')) ?>"
                >
                    Zimmer buchen
                </a>

            </div>

        </div>

    </div>
</section>

<!-- Atmosphäre -->
<section
    class="hotel-atmosphere-section py-5"
    aria-labelledby="atmosphere-title"
>
    <div class="container">

        <div class="row align-items-center g-5">

            <div class="col-lg-7">

                <span class="hotel-section-kicker hotel-section-kicker-light">
                    Mehr als eine Übernachtung
                </span>

                <h2
                    id="atmosphere-title"
                    class="hotel-featurette-title text-white"
                >
                    Ein Ort, an dem<br>
                    man gerne bleibt.
                </h2>

                <p class="lead text-white">
                    Starten Sie entspannt in den Tag und lassen Sie den Abend
                    in angenehmer Atmosphäre ausklingen.
                </p>

                <p class="text-white-50">
                    Durch unsere Gastronomie und die persönliche Betreuung
                    wird Ihr Aufenthalt zu mehr als einer einfachen
                    Übernachtung.
                </p>

                <a
                    class="btn btn-hotel-light mt-3"
                    href="<?= $escape($url('/gastronomie/')) ?>"
                >
                    Gastronomie entdecken
                </a>

            </div>

            <div class="col-lg-5">

                <div class="hotel-atmosphere-box">

                    <div class="hotel-atmosphere-icon">
                        ✦
                    </div>

                    <h3>
                        Herzlich willkommen
                    </h3>

                    <p>
                        Wir freuen uns darauf, Sie persönlich bei uns
                        begrüßen zu dürfen.
                    </p>

                </div>

            </div>

        </div>

    </div>
</section>

<!-- Buchungs-CTA -->
<section
    class="hotel-booking-section py-5"
    aria-labelledby="hotel-booking-title"
>
    <div class="container">

        <div class="hotel-booking-card">

            <div class="row align-items-center g-4">

                <div class="col-lg-8">

                    <span class="hotel-section-kicker">
                        Jetzt planen
                    </span>

                    <h2 id="hotel-booking-title">
                        Ihre Übernachtung beginnt hier.
                    </h2>

                    <p class="mb-0">
                        Nehmen Sie Kontakt mit uns auf oder buchen Sie
                        direkt Ihr Zimmer.
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