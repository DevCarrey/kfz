<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Ausgabe puffern
|--------------------------------------------------------------------------
|
| Die Ausgabe wird zunächst zwischengespeichert.
| Dadurch können Weiterleitungen und Sessions verarbeitet werden,
| bevor HTML an den Browser gesendet wird.
|--------------------------------------------------------------------------
*/

if (ob_get_level() === 0) {
    ob_start();
}


/*
|--------------------------------------------------------------------------
| Session starten
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
| App-Prefix automatisch ermitteln
|--------------------------------------------------------------------------
|
| Beispiel:
| C:\xampp\htdocs\kfz\index.php
|
| Website:
| http://localhost/kfz/
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

require_once __DIR__ . '/src/Support/view_helpers.php';


/*
|--------------------------------------------------------------------------
| Hilfsfunktion für Includes
|--------------------------------------------------------------------------
*/

function safe_include(string $file): void
{
    kfz_safe_include($file);
}


/*
|--------------------------------------------------------------------------
| Aktuelle Route ermitteln
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
     * App-Prefix entfernen.
     *
     * Beispiel:
     * /kfz/login/
     *
     * wird zu:
     * /login/
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

    return $parts[0] !== ''
        ? $parts[0]
        : 'home';
}


/*
|--------------------------------------------------------------------------
| Logout verarbeiten
|--------------------------------------------------------------------------
|
| Logout muss vor dem Header ausgeführt werden,
| damit die Weiterleitung ohne Header-Fehler funktioniert.
|--------------------------------------------------------------------------
*/

$route = getRoute($appPrefix);

if ($route === 'logout') {

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {

        $sessionCookieParams = session_get_cookie_params();

        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => (string)$sessionCookieParams['path'],
            'domain' => (string)$sessionCookieParams['domain'],
            'secure' => (bool)$sessionCookieParams['secure'],
            'httponly' => (bool)$sessionCookieParams['httponly'],
            'samesite' => (string)($sessionCookieParams['samesite'] ?? 'Lax'),
        ]);
    }

    session_destroy();

    $loginUrl = $appPrefix === ''
        ? '/login/'
        : $appPrefix . '/login/';

    if (ob_get_level() > 0) {
        ob_end_clean();
    }

    header(
        'Location: ' . $loginUrl,
        true,
        303
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Erlaubte Seiten
|--------------------------------------------------------------------------
*/

$allowedRoutes = [
    'home',
    'vorgaenge',
    'fahrzeuge',
    'hilfe',
    'kontakt',
    'login',
    'register',
    'konto',
    'vorgang-starten',
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
        'description' => 'Mit Kfz Digital bereiten Sie die Abmeldung Ihres Fahrzeugs digital vor.',
    ],

    'vorgaenge' => [
        'title' => 'Meine Vorgänge – Kfz Digital',
        'description' => 'Übersicht und Status Ihrer digitalen Fahrzeugvorgänge.',
    ],

    'fahrzeuge' => [
        'title' => 'Meine Fahrzeuge – Kfz Digital',
        'description' => 'Verwalten Sie Ihre Fahrzeuge übersichtlich an einem Ort.',
    ],

    'hilfe' => [
        'title' => 'Hilfe – Kfz Digital',
        'description' => 'Antworten und Hilfestellungen rund um digitale Fahrzeugvorgänge.',
    ],

    'kontakt' => [
        'title' => 'Kontakt – Kfz Digital',
        'description' => 'Nehmen Sie Kontakt mit Kfz Digital auf.',
    ],

    'login' => [
        'title' => 'Anmelden – Kfz Digital',
        'description' => 'Melden Sie sich bei Kfz Digital an.',
    ],

    'register' => [
        'title' => 'Konto erstellen – Kfz Digital',
        'description' => 'Erstellen Sie Ihr persönliches Konto bei Kfz Digital.',
    ],

    'konto' => [
        'title' => 'Mein Konto – Kfz Digital',
        'description' => 'Verwalten Sie Ihr persönliches Kfz-Digital-Konto.',
    ],

    'vorgang-starten' => [
        'title' => 'Fahrzeug abmelden – Kfz Digital',
        'description' => 'Bereiten Sie die digitale Abmeldung Ihres Fahrzeugs vor.',
    ],

    'ueber-kfz-digital' => [
        'title' => 'Über Kfz Digital',
        'description' => 'Erfahren Sie mehr über Kfz Digital und unsere digitale Plattform.',
    ],

    'faq' => [
        'title' => 'FAQ – Kfz Digital',
        'description' => 'Häufig gestellte Fragen zu Kfz Digital.',
    ],

    'impressum' => [
        'title' => 'Impressum – Kfz Digital',
        'description' => 'Impressum von Kfz Digital.',
    ],

    'datenschutz' => [
        'title' => 'Datenschutz – Kfz Digital',
        'description' => 'Datenschutzerklärung von Kfz Digital.',
    ],

    'nutzungsbedingungen' => [
        'title' => 'Nutzungsbedingungen – Kfz Digital',
        'description' => 'Nutzungsbedingungen von Kfz Digital.',
    ],

    '404' => [
        'title' => 'Seite nicht gefunden – Kfz Digital',
        'description' => 'Die angeforderte Seite wurde nicht gefunden.',
    ],
];


$pageTitle = $pageData[$route]['title']
    ?? 'Kfz Digital';

$pageDescription = $pageData[$route]['description']
    ?? 'Fahrzeug online abmelden – einfach, sicher und digital mit Kfz Digital.';

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
| 404-Datei optional
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

if ($pageFile !== null) {

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
