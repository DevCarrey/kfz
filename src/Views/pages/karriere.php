<?php
declare(strict_types=1);

$appPrefix = rtrim((string)($GLOBALS['appPrefix'] ?? ''), '/');

/**
 * Erstellt URLs zu Dateien im public-Verzeichnis.
 */
$asset = static function (string $path) use ($appPrefix): string {
    return $appPrefix . '/public/' . ltrim($path, '/');
};

/**
 * Erstellt interne URLs.
 */
$url = static function (string $path) use ($appPrefix): string {
    return $appPrefix . '/' . ltrim($path, '/');
};

/**
 * Sicheres Escaping für HTML-Ausgaben.
 */
$escape = static function (string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
};

$jobAreas = [
    [
        'icon'  => '🚚',
        'title' => 'Logistik und Auslieferung',
        'text'  => 'In der Logistik sorgen Sie dafür, dass Getränke und Equipment zuverlässig, pünktlich und ordentlich bei unseren Kunden ankommen.',
    ],
    [
        'icon'  => '📦',
        'title' => 'Lager und Warenfluss',
        'text'  => 'Ein gut organisierter Warenfluss ist die Grundlage für reibungslose Abläufe in unserem Unternehmen.',
    ],
    [
        'icon'  => '💬',
        'title' => 'Vertrieb und Beratung',
        'text'  => 'Unsere Kundenberatung verbindet Fachwissen, persönliche Gespräche und ein gutes Verständnis für Gastronomie und Veranstaltungen.',
    ],
    [
        'icon'  => '🎪',
        'title' => 'Events und Gastronomie',
        'text'  => 'Bei Veranstaltungen ist Organisation gefragt. Hier zählt ein gutes Zusammenspiel aus Planung, Service und praktischer Umsetzung.',
    ],
];

$reasons = [
    [
        'number' => '01',
        'title'  => 'Abwechslungsreiche Aufgaben',
        'text'   => 'Getränke, Gastronomie, Logistik und Veranstaltungen sorgen für einen vielseitigen Arbeitsalltag.',
    ],
    [
        'number' => '02',
        'title'  => 'Arbeiten mit Verantwortung',
        'text'   => 'Wir setzen auf Menschen, die mitdenken, Verantwortung übernehmen und Dinge zuverlässig umsetzen.',
    ],
    [
        'number' => '03',
        'title'  => 'Ein persönliches Umfeld',
        'text'   => 'Als regional verbundenes Unternehmen legen wir Wert auf kurze Wege und direkte Kommunikation.',
    ],
];
?>

<!-- Seitenkopf -->
<section class="py-5 bg-dark text-white" aria-labelledby="career-title">
    <div class="container py-lg-5">
        <div class="row align-items-center g-5">

            <div class="col-lg-7">
                <p class="text-warning text-uppercase fw-bold mb-3">
                    Karriere bei Getränke Vogt
                </p>

                <h1 id="career-title" class="display-3 fw-bold mb-4">
                    Arbeiten, wo
                    <br>
                    etwas bewegt wird.
                </h1>

                <p class="lead text-white-50 mb-0">
                    Hinter jeder Lieferung, jeder Veranstaltung und jedem zufriedenen
                    Kunden steht ein Team, das zusammenarbeitet und Verantwortung übernimmt.
                </p>
            </div>

            <div class="col-lg-5">
                <div
                    class="rounded-4 bg-warning text-dark p-5 shadow-sm"
                    role="img"
                    aria-label="Bildplatzhalter für den Karrierebereich"
                >
                    <div class="display-1 mb-4" aria-hidden="true">
                        👋
                    </div>

                    <h2 class="h3 fw-bold">
                        Werden Sie Teil unseres Teams
                    </h2>

                    <p class="mb-0">
                        Ob Logistik, Lager, Vertrieb oder Veranstaltung:
                        Bei uns zählen Einsatzbereitschaft und Teamgeist.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Einstieg -->
<section class="py-5" aria-labelledby="work-title">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 text-center">

                <p class="text-warning text-uppercase fw-bold mb-2">
                    Gemeinsam anpacken
                </p>

                <h2 id="work-title" class="display-6 fw-bold mb-4">
                    Getränke Vogt ist mehr als Lieferung und Logistik.
                </h2>

                <p class="lead text-secondary">
                    Unser Arbeitsalltag ist vielseitig, praktisch und nah an den Menschen.
                </p>

                <p class="text-secondary mb-0">
                    Wir arbeiten für Gastronomiebetriebe, Veranstaltungen und private Kunden.
                    Dafür braucht es zuverlässige Abläufe, gute Kommunikation und Menschen,
                    die auch dann den Überblick behalten, wenn es einmal schnell gehen muss.
                </p>

            </div>
        </div>
    </div>
</section>

<!-- Bildplatzhalter -->
<section class="py-5 bg-light" aria-labelledby="workplace-title">
    <div class="container">
        <div class="row align-items-center g-5">

            <div class="col-lg-6">
                <div
                    class="rounded-4 bg-warning d-flex align-items-center justify-content-center"
                    style="min-height: 390px;"
                    role="img"
                    aria-label="Bildplatzhalter für Mitarbeiter bei Getränke Vogt"
                >
                    <div class="text-center">
                        <div class="display-1 mb-3" aria-hidden="true">
                            👥
                        </div>

                        <p class="fw-semibold mb-1">
                            Platzhalter für Mitarbeiterfoto
                        </p>

                        <small>
                            Zum Beispiel: Team im Lager,
                            bei der Auslieferung oder im Einsatz
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <p class="text-warning text-uppercase fw-bold mb-2">
                    Unser Arbeitsumfeld
                </p>

                <h2 id="workplace-title" class="display-6 fw-bold mb-4">
                    Jeder Bereich trägt zum gemeinsamen Erfolg bei.
                </h2>

                <p class="lead">
                    Von der Bestellung bis zur Auslieferung greifen viele Arbeitsschritte
                    ineinander.
                </p>

                <p class="text-secondary">
                    Deshalb ist uns wichtig, dass sich alle aufeinander verlassen können.
                    Gute Zusammenarbeit bedeutet für uns, Informationen weiterzugeben,
                    mit anzupacken und gemeinsam Lösungen zu finden.
                </p>

                <p class="text-secondary mb-0">
                    Wer gerne praktisch arbeitet, Verantwortung übernimmt und den Kontakt
                    zu Menschen schätzt, findet bei uns vielseitige Aufgaben.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- Arbeitsbereiche -->
<section class="py-5" aria-labelledby="areas-title">
    <div class="container">

        <div class="row align-items-end g-4 mb-5">
            <div class="col-lg-7">
                <p class="text-warning text-uppercase fw-bold mb-2">
                    Mögliche Einsatzbereiche
                </p>

                <h2 id="areas-title" class="display-6 fw-bold mb-0">
                    Wo Sie bei uns mitwirken können.
                </h2>
            </div>

            <div class="col-lg-5">
                <p class="text-secondary mb-0">
                    Unsere Aufgaben sind unterschiedlich — das Ziel bleibt dasselbe:
                    zuverlässiger Service für unsere Kunden.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($jobAreas as $area): ?>
                <div class="col-md-6 col-lg-3">
                    <article class="h-100 border rounded-4 p-4">

                        <div class="display-5 mb-4" aria-hidden="true">
                            <?= $escape($area['icon']) ?>
                        </div>

                        <h3 class="h4 fw-bold">
                            <?= $escape($area['title']) ?>
                        </h3>

                        <p class="text-secondary mb-0">
                            <?= $escape($area['text']) ?>
                        </p>

                    </article>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- Gründe -->
<section class="py-5 bg-dark text-white" aria-labelledby="reasons-title">
    <div class="container">

        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <p class="text-warning text-uppercase fw-bold mb-2">
                    Was uns wichtig ist
                </p>

                <h2 id="reasons-title" class="display-6 fw-bold">
                    Gute Arbeit entsteht im Team.
                </h2>

                <p class="text-white-50 mb-0">
                    Wir suchen keine perfekten Lebensläufe, sondern Menschen,
                    die zu uns passen und sich einbringen möchten.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($reasons as $reason): ?>
                <div class="col-md-4">
                    <article class="h-100 border border-secondary rounded-4 p-4">

                        <div class="display-6 text-warning fw-bold mb-4">
                            <?= $escape($reason['number']) ?>
                        </div>

                        <h3 class="h4 fw-bold">
                            <?= $escape($reason['title']) ?>
                        </h3>

                        <p class="text-white-50 mb-0">
                            <?= $escape($reason['text']) ?>
                        </p>

                    </article>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- Anforderungen -->
<section class="py-5" aria-labelledby="requirements-title">
    <div class="container">
        <div class="row g-5 align-items-center">

            <div class="col-lg-5">
                <div
                    class="rounded-4 bg-light d-flex align-items-center justify-content-center"
                    style="min-height: 360px;"
                    role="img"
                    aria-label="Bildplatzhalter für Arbeitsplatz oder Fuhrpark"
                >
                    <div class="text-center text-secondary">
                        <div class="display-1 mb-3" aria-hidden="true">
                            🚚
                        </div>

                        <p class="mb-0">
                            Platzhalter für Arbeitsplatzfoto
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <p class="text-warning text-uppercase fw-bold mb-2">
                    Das sollten Sie mitbringen
                </p>

                <h2 id="requirements-title" class="display-6 fw-bold mb-4">
                    Einsatzbereitschaft, Zuverlässigkeit und Freude an guter Zusammenarbeit.
                </h2>

                <ul class="list-unstyled text-secondary mb-0">
                    <li class="d-flex gap-3 mb-3">
                        <span class="text-warning fw-bold fs-4">✓</span>
                        <span>Sie arbeiten zuverlässig und verantwortungsbewusst.</span>
                    </li>

                    <li class="d-flex gap-3 mb-3">
                        <span class="text-warning fw-bold fs-4">✓</span>
                        <span>Sie behalten auch bei wechselnden Aufgaben den Überblick.</span>
                    </li>

                    <li class="d-flex gap-3 mb-3">
                        <span class="text-warning fw-bold fs-4">✓</span>
                        <span>Sie arbeiten gerne im Team und packen mit an.</span>
                    </li>

                    <li class="d-flex gap-3">
                        <span class="text-warning fw-bold fs-4">✓</span>
                        <span>Sie gehen freundlich und professionell mit Kunden und Kollegen um.</span>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</section>

<!-- Bewerbung -->
<section class="py-5 bg-light" aria-labelledby="application-title">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">

                <div class="border border-2 border-warning rounded-4 p-4 p-lg-5 text-center">

                    <p class="text-warning text-uppercase fw-bold mb-2">
                        Ihre Bewerbung
                    </p>

                    <h2 id="application-title" class="display-6 fw-bold mb-4">
                        Sie können sich vorstellen, zu uns zu passen?
                    </h2>

                    <p class="lead text-secondary">
                        Dann freuen wir uns, von Ihnen zu hören.
                    </p>

                    <p class="text-secondary mb-4">
                        Auch wenn aktuell keine passende Stelle ausgeschrieben ist,
                        können Sie gerne Kontakt zu uns aufnehmen und sich persönlich vorstellen.
                    </p>

                    <a
                        class="btn btn-warning btn-lg"
                        href="<?= $escape($url('/contact/')) ?>"
                    >
                        Kontakt aufnehmen
                    </a>

                </div>

            </div>
        </div>
    </div>
</section>

<!-- Galerie-Platzhalter -->
<section class="py-5" aria-labelledby="gallery-title">
    <div class="container">

        <div class="text-center mb-5">
            <p class="text-warning text-uppercase fw-bold mb-2">
                Einblicke in den Arbeitsalltag
            </p>

            <h2 id="gallery-title" class="display-6 fw-bold">
                Hier ist Platz für echte Teamfotos.
            </h2>

            <p class="text-secondary mb-0">
                Die Platzhalter können später durch Bilder aus Lager, Fuhrpark,
                Büro oder Veranstaltungseinsätzen ersetzt werden.
            </p>
        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <div
                    class="rounded-4 bg-light border d-flex align-items-center justify-content-center"
                    style="min-height: 240px;"
                    role="img"
                    aria-label="Bildplatzhalter für Mitarbeiter im Lager"
                >
                    <div class="text-center text-secondary">
                        <div class="display-3 mb-3" aria-hidden="true">
                            📦
                        </div>

                        <p class="mb-0">
                            Platzhalter<br>
                            Lager und Warenfluss
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div
                    class="rounded-4 bg-light border d-flex align-items-center justify-content-center"
                    style="min-height: 240px;"
                    role="img"
                    aria-label="Bildplatzhalter für Fahrer und Auslieferung"
                >
                    <div class="text-center text-secondary">
                        <div class="display-3 mb-3" aria-hidden="true">
                            🚛
                        </div>

                        <p class="mb-0">
                            Platzhalter<br>
                            Auslieferung und Fuhrpark
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div
                    class="rounded-4 bg-light border d-flex align-items-center justify-content-center"
                    style="min-height: 240px;"
                    role="img"
                    aria-label="Bildplatzhalter für das Team bei einer Veranstaltung"
                >
                    <div class="text-center text-secondary">
                        <div class="display-3 mb-3" aria-hidden="true">
                            🎉
                        </div>

                        <p class="mb-0">
                            Platzhalter<br>
                            Veranstaltungseinsatz
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Abschluss -->
<section class="py-5 bg-warning" aria-labelledby="contact-title">
    <div class="container">
        <div class="row align-items-center g-4">

            <div class="col-lg-8">
                <h2 id="contact-title" class="fw-bold mb-2">
                    Wir freuen uns auf Ihre Nachricht.
                </h2>

                <p class="mb-0">
                    Erzählen Sie uns etwas über sich und Ihren beruflichen Weg.
                </p>
            </div>

            <div class="col-lg-4 text-lg-end">
                <a
                    class="btn btn-dark btn-lg"
                    href="<?= $escape($url('/contact/')) ?>"
                >
                    Kontakt aufnehmen
                </a>
            </div>

        </div>
    </div>
</section>