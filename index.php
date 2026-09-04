<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Ausgabe immer puffern
|--------------------------------------------------------------------------
*/

while (ob_get_level() > 0) {
    ob_end_clean();
}

ob_start();


/*
|--------------------------------------------------------------------------
| Session starten
|--------------------------------------------------------------------------
|
| Die Session wird weiterhin für technische Zwecke verwendet:
| - CSRF-Token
| - temporäre Formulardaten
| - Bestätigungscode
| - Fehlversuche
|
| Es gibt keinen verpflichtenden Kundenlogin.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/src/Bootstrap/session.php';


/*
|--------------------------------------------------------------------------
| Projektpfade
|--------------------------------------------------------------------------
*/

$projectDir = __DIR__;
$srcDir = $projectDir . '/src';


/*
|--------------------------------------------------------------------------
| App-Prefix ermitteln
|--------------------------------------------------------------------------
|
| Beispiel:
| http://localhost/kfz/
|
| Ergebnis:
| /kfz
|
| Bei einer eigenen Domain:
| https://kfz-digital.de/
|
| Ergebnis:
| ''
|--------------------------------------------------------------------------
*/

$scriptName = str_replace(
    '\\',
    '/',
    (string)($_SERVER['SCRIPT_NAME'] ?? '/index.php')
);

$baseUri = rtrim(
    dirname($scriptName),
    '/'
);

$appPrefix = (
    $baseUri === '/'
    || $baseUri === '.'
)
    ? ''
    : $baseUri;

$GLOBALS['appPrefix'] = $appPrefix;


/*
|--------------------------------------------------------------------------
| Zentrale Hilfsfunktionen laden
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/src/Support/view_helpers.php';


/*
|--------------------------------------------------------------------------
| Sichere Include-Funktion
|--------------------------------------------------------------------------
*/

function safe_include(string $file): void
{
    kfz_safe_include($file);
}


/*
|--------------------------------------------------------------------------
| Route ermitteln
|--------------------------------------------------------------------------
*/

function getRoute(string $appPrefix): string
{
    $requestUri = (string)(
        $_SERVER['REQUEST_URI'] ?? '/'
    );

    $path = parse_url(
        $requestUri,
        PHP_URL_PATH
    );

    if (!is_string($path) || $path === '') {
        $path = '/';
    }

    $path = str_replace(
        '\\',
        '/',
        $path
    );

    /*
     * Projekt-Prefix entfernen.
     *
     * Beispiel:
     * /kfz/vorgang-starten/
     *
     * wird zu:
     * /vorgang-starten/
     */

    if (
        $appPrefix !== ''
        && (
            $path === $appPrefix
            || str_starts_with(
                $path,
                $appPrefix . '/'
            )
        )
    ) {
        $path = substr(
            $path,
            strlen($appPrefix)
        );
    }

    $path = trim(
        $path,
        '/'
    );

    /*
     * Leere Route = Startseite
     */

    if ($path === '') {
        return 'home';
    }

    /*
     * Nur das erste URL-Segment verwenden
     */

    $parts = explode(
        '/',
        $path
    );

    $route = $parts[0] ?? '';

    return $route !== ''
        ? $route
        : 'home';
}


/*
|--------------------------------------------------------------------------
| Route laden
|--------------------------------------------------------------------------
*/

$route = getRoute($appPrefix);


/*
|--------------------------------------------------------------------------
| Öffentliche erlaubte Routen
|--------------------------------------------------------------------------
*/

$allowedRoutes = [
    'home',
    'vorgang-starten',
    'vorgang-pruefen',
    'zahlung',
    'zahlung-erfolgreich',
    'zahlung-abgebrochen',
    'hilfe',
    'kontakt',
    'ueber-kfz-digital',
    'faq',
    'impressum',
    'datenschutz',
    'nutzungsbedingungen',
];

/*
|--------------------------------------------------------------------------
| Ungültige Route auf 404 setzen
|--------------------------------------------------------------------------
*/

if (!in_array($route, $allowedRoutes, true)) {
    http_response_code(404);

    $route = '404';
}


/*
|--------------------------------------------------------------------------
| Seitenspezifische Meta-Daten
|--------------------------------------------------------------------------
*/

$pageData = [

    'home' => [
        'title' => 'Kfz Digital – Fahrzeug online abmelden',
        'description' =>
            'Fahrzeug online abmelden – einfach, übersichtlich und digital.',
    ],

    'vorgang-starten' => [
        'title' => 'Fahrzeug abmelden – Kfz Digital',
        'description' =>
            'Starten Sie Ihre digitale Fahrzeugabmeldung ohne Registrierung.',
    ],

    'vorgang-pruefen' => [
        'title' => 'Vorgang prüfen – Kfz Digital',
        'description' =>
            'Prüfen Sie den aktuellen Status Ihres Fahrzeugvorgangs.',
    ],

    'hilfe' => [
        'title' => 'Hilfe – Kfz Digital',
        'description' =>
            'Hilfe und Informationen zur digitalen Fahrzeugabmeldung.',
    ],

    'kontakt' => [
        'title' => 'Kontakt – Kfz Digital',
        'description' =>
            'Kontaktieren Sie den Kundenservice von Kfz Digital.',
    ],

    'ueber-kfz-digital' => [
        'title' => 'Über Kfz Digital',
        'description' =>
            'Erfahren Sie mehr über Kfz Digital und den digitalen Abmeldeprozess.',
    ],

    'faq' => [
        'title' => 'FAQ – Kfz Digital',
        'description' =>
            'Häufig gestellte Fragen zur digitalen Fahrzeugabmeldung.',
    ],

    'impressum' => [
        'title' => 'Impressum – Kfz Digital',
        'description' =>
            'Impressum von Kfz Digital.',
    ],

    'datenschutz' => [
        'title' => 'Datenschutz – Kfz Digital',
        'description' =>
            'Informationen zum Datenschutz bei Kfz Digital.',
    ],

    'nutzungsbedingungen' => [
        'title' => 'Nutzungsbedingungen – Kfz Digital',
        'description' =>
            'Nutzungsbedingungen von Kfz Digital.',
    ],

    '404' => [
        'title' => 'Seite nicht gefunden – Kfz Digital',
        'description' =>
            'Die angeforderte Seite konnte nicht gefunden werden.',
    ],
'zahlung' => [
    'title' => 'Zahlung – Kfz Digital',
    'description' =>
        'Zahlung für Ihre digitale Fahrzeugabmeldung.',
],

'zahlung-erfolgreich' => [
    'title' => 'Zahlung erfolgreich – Kfz Digital',
    'description' =>
        'Ihre Zahlung wurde erfolgreich bestätigt.',
],

'zahlung-abgebrochen' => [
    'title' => 'Zahlung abgebrochen – Kfz Digital',
    'description' =>
        'Die Zahlung wurde abgebrochen.',
],
];


$pageTitle = $pageData[$route]['title']
    ?? 'Kfz Digital';

$pageDescription = $pageData[$route]['description']
    ?? 'Fahrzeug online abmelden – einfach und digital.';


/*
|--------------------------------------------------------------------------
| Canonical-Pfad bestimmen
|--------------------------------------------------------------------------
*/

$canonicalPath = $route === 'home'
    ? '/'
    : '/' . $route . '/';


/*
|--------------------------------------------------------------------------
| Seiten-Datei bestimmen
|--------------------------------------------------------------------------
*/

$pageFile = $srcDir
    . '/Views/pages/'
    . $route
    . '.php';


/*
|--------------------------------------------------------------------------
| 404-Datei prüfen
|--------------------------------------------------------------------------
*/

if (
    $route === '404'
    && !is_file($pageFile)
) {
    $pageFile = null;
}


/*
|--------------------------------------------------------------------------
| Standardwerte für Seiten
|--------------------------------------------------------------------------
*/

$GLOBALS['render_process_on_home'] = false;


/*
|--------------------------------------------------------------------------
| Header laden
|--------------------------------------------------------------------------
*/

safe_include(
    $srcDir . '/Views/layout/header.php'
);


/*
|--------------------------------------------------------------------------
| Seite laden
|--------------------------------------------------------------------------
*/

if ($pageFile !== null && is_file($pageFile)) {

    safe_include($pageFile);

} else {

    ?>

    <section class="kfz-section">

        <div class="container text-center">

            <span class="kfz-section-kicker">
                Fehler 404
            </span>

            <h1 class="kfz-section-title">
                Seite nicht gefunden
            </h1>

            <p class="kfz-section-text">
                Die gewünschte Seite konnte nicht gefunden werden.
            </p>

            <a
                class="kfz-button kfz-button-primary"
                href="<?= htmlspecialchars(
                    $appPrefix . '/',
                    ENT_QUOTES | ENT_SUBSTITUTE,
                    'UTF-8'
                ) ?>"
            >
                Zur Startseite
            </a>

        </div>

    </section>

    <?php
}


/*
|--------------------------------------------------------------------------
| Footer laden
|--------------------------------------------------------------------------
*/

safe_include(
    $srcDir . '/Views/layout/footer.php'
);


/*
|--------------------------------------------------------------------------
| Ausgabe abschließen
|--------------------------------------------------------------------------
*/

if (ob_get_level() > 0) {
    ob_end_flush();
}