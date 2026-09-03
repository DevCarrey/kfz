<?php
declare(strict_types=1);

/**
 * Kfz Digital – globaler Header
 *
 * Der Header wird über index.php eingebunden.
 *
 * Wichtig:
 * Session und Ausgabe-Pufferung werden in index.php gestartet.
 */


/*
|--------------------------------------------------------------------------
| App-Prefix
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../Support/view_helpers.php';

$appPrefix = kfz_app_prefix();


/*
|--------------------------------------------------------------------------
| URL-Hilfsfunktionen
|--------------------------------------------------------------------------
*/

$url = static fn (string $path): string => kfz_url($path);

$asset = static fn (string $path): string => kfz_asset_url($path);

$escape = static fn (string $value): string => kfz_escape($value);


/*
|--------------------------------------------------------------------------
| Aktuellen Pfad ermitteln
|--------------------------------------------------------------------------
*/

$currentPath = parse_url(
    (string)($_SERVER['REQUEST_URI'] ?? '/'),
    PHP_URL_PATH
);

$currentPath = is_string($currentPath)
    ? rtrim($currentPath, '/')
    : '';

if ($currentPath === '') {
    $currentPath = '/';
}


/*
|--------------------------------------------------------------------------
| Menüpunkt aktiv?
|--------------------------------------------------------------------------
*/

$isActive = static function (string $path) use (
    $appPrefix,
    $currentPath
): bool {
    $path = '/' . ltrim($path, '/');

    $fullPath = $appPrefix . (
        $path === '/'
            ? '/'
            : $path
    );

    $fullPath = rtrim($fullPath, '/');

    if ($fullPath === '') {
        $fullPath = '/';
    }

    if ($fullPath === '/') {
        return $currentPath === '/';
    }

    return $currentPath === $fullPath
        || str_starts_with(
            $currentPath,
            $fullPath . '/'
        );
};


/*
|--------------------------------------------------------------------------
| Benutzerstatus
|--------------------------------------------------------------------------
*/

$isLoggedIn = kfz_is_logged_in();


/*
|--------------------------------------------------------------------------
| Logo prüfen
|--------------------------------------------------------------------------
*/

$logoPath = __DIR__
    . '/../../../public/assets/img/LOGO.png';

$hasLogo = is_file($logoPath);
?>

<!DOCTYPE html>
<html lang="de">

<head>

    <?php include __DIR__ . '/meta.php'; ?>

</head>

<body>

<a
    class="skip-link"
    href="#content"
>
    Zum Inhalt springen
</a>


<header class="kfz-main-header">

    <div class="container">

        <div class="kfz-header-inner">

            <!-- Logo und Markenname -->

            <a
                href="<?= $escape($url('/')) ?>"
                class="kfz-brand"
                aria-label="Kfz Digital – Startseite"
            >

                <?php if ($hasLogo): ?>

                    <img
                        src="<?= $escape($asset('/assets/img/LOGO.png')) ?>"
                        alt="Kfz Digital"
                        class="kfz-brand-logo"
                    >

                <?php else: ?>

                    <span
                        class="kfz-brand-icon"
                        aria-hidden="true"
                    >
                        <svg
                            viewBox="0 0 64 64"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M13 38h38l-3-13a5 5 0 0 0-4.8-3.8H20.8A5 5 0 0 0 16 25L13 38Z"
                            />

                            <path d="M9 38h46v11H9z" />

                            <circle
                                cx="19"
                                cy="49"
                                r="5"
                            />

                            <circle
                                cx="45"
                                cy="49"
                                r="5"
                            />

                            <path d="M17 30h30" />
                        </svg>
                    </span>

                <?php endif; ?>


                <span class="kfz-brand-text">

                    <span class="kfz-brand-name">
                        Kfz Digital
                    </span>

                    <span class="kfz-brand-slogan">
                        Fahrzeug abmelden – einfach digital
                    </span>

                </span>

            </a>


            <!-- Mobile-Menübutton -->

            <button
                type="button"
                id="kfzMenuToggle"
                class="kfz-menu-toggle"
                aria-controls="kfzMainNavigation"
                aria-expanded="false"
            >

                <span class="visually-hidden">
                    Navigation öffnen
                </span>

                <span class="kfz-menu-toggle-line"></span>
                <span class="kfz-menu-toggle-line"></span>
                <span class="kfz-menu-toggle-line"></span>

            </button>


            <!-- Hauptnavigation -->

            <nav
                id="kfzMainNavigation"
                class="kfz-main-navigation"
                aria-label="Hauptnavigation"
            >

                <ul class="kfz-navigation-list">


                    <!-- Startseite -->

                    <li class="kfz-navigation-item">

                        <a
                            href="<?= $escape($url('/')) ?>"
                            class="kfz-navigation-link <?= $isActive('/') ? 'active' : '' ?>"
                            <?= $isActive('/') ? 'aria-current="page"' : '' ?>
                        >

                            <svg
                                class="kfz-navigation-icon"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10Z" />
                            </svg>

                            <span>
                                Startseite
                            </span>

                        </a>

                    </li>


                    <!-- Vorgänge -->

                    <li class="kfz-navigation-item">

                        <a
                            href="<?= $escape($url('/vorgaenge/')) ?>"
                            class="kfz-navigation-link <?= $isActive('/vorgaenge/') ? 'active' : '' ?>"
                            <?= $isActive('/vorgaenge/') ? 'aria-current="page"' : '' ?>
                        >

                            <svg
                                class="kfz-navigation-icon"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path d="M6 3h9l3 3v15H6V3Z" />
                                <path d="M14 3v4h4M9 12h6M9 16h6" />
                            </svg>

                            <span>
                                Meine Vorgänge
                            </span>

                        </a>

                    </li>


                    <!-- Fahrzeuge -->

                    <li class="kfz-navigation-item">

                        <a
                            href="<?= $escape($url('/fahrzeuge/')) ?>"
                            class="kfz-navigation-link <?= $isActive('/fahrzeuge/') ? 'active' : '' ?>"
                            <?= $isActive('/fahrzeuge/') ? 'aria-current="page"' : '' ?>
                        >

                            <svg
                                class="kfz-navigation-icon"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path d="M5 16h14l-1.5-6H6.5L5 16Z" />
                                <path d="M4 16h16v4H4v-4ZM7 20v1m10-1v1M7 10l1.5-3h7L17 10" />
                                <circle cx="8" cy="16" r="1" />
                                <circle cx="16" cy="16" r="1" />
                            </svg>

                            <span>
                                Meine Fahrzeuge
                            </span>

                        </a>

                    </li>


                    <!-- Hilfe -->

                    <li class="kfz-navigation-item">

                        <a
                            href="<?= $escape($url('/hilfe/')) ?>"
                            class="kfz-navigation-link <?= $isActive('/hilfe/') ? 'active' : '' ?>"
                            <?= $isActive('/hilfe/') ? 'aria-current="page"' : '' ?>
                        >

                            <svg
                                class="kfz-navigation-icon"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <circle
                                    cx="12"
                                    cy="12"
                                    r="9"
                                />

                                <path d="M9.5 9a2.5 2.5 0 1 1 4.2 1.8c-1.1.9-1.7 1.3-1.7 2.7M12 17h.01" />
                            </svg>

                            <span>
                                Hilfe
                            </span>

                        </a>

                    </li>


                    <!-- Kontakt -->

                    <li class="kfz-navigation-item">

                        <a
                            href="<?= $escape($url('/kontakt/')) ?>"
                            class="kfz-navigation-link <?= $isActive('/kontakt/') ? 'active' : '' ?>"
                            <?= $isActive('/kontakt/') ? 'aria-current="page"' : '' ?>
                        >

                            <svg
                                class="kfz-navigation-icon"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path d="M4 5h16v12H8l-4 4V5Z" />
                                <path d="M8 9h8M8 13h5" />
                            </svg>

                            <span>
                                Kontakt
                            </span>

                        </a>

                    </li>


                    <!-- Hauptaktion -->

                    <li class="kfz-navigation-item kfz-navigation-action">

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

                            <span>
                                Fahrzeug abmelden
                            </span>

                        </a>

                    </li>


                    <!-- Benutzerbereich -->

                    <li class="kfz-navigation-item kfz-account-item">

                        <?php if ($isLoggedIn): ?>

                            <a
                                href="<?= $escape($url('/konto/')) ?>"
                                class="kfz-account-link <?= $isActive('/konto/') ? 'active' : '' ?>"
                                <?= $isActive('/konto/') ? 'aria-current="page"' : '' ?>
                                aria-label="Mein Konto öffnen"
                            >

                                <span
                                    class="kfz-account-icon"
                                    aria-hidden="true"
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <circle
                                            cx="12"
                                            cy="8"
                                            r="3.5"
                                        />

                                        <path d="M5 20a7 7 0 0 1 14 0" />
                                    </svg>
                                </span>

                                <span>
                                    Mein Konto
                                </span>

                            </a>


                            <a
                                href="<?= $escape($url('/logout/')) ?>"
                                class="kfz-account-logout"
                            >
                                Abmelden
                            </a>

                        <?php else: ?>

                            <a
                                href="<?= $escape($url('/login/')) ?>"
                                class="kfz-account-link <?= $isActive('/login/') ? 'active' : '' ?>"
                                <?= $isActive('/login/') ? 'aria-current="page"' : '' ?>
                            >

                                <span
                                    class="kfz-account-icon"
                                    aria-hidden="true"
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <circle
                                            cx="12"
                                            cy="8"
                                            r="3.5"
                                        />

                                        <path d="M5 20a7 7 0 0 1 14 0" />
                                    </svg>
                                </span>

                                <span>
                                    Anmelden
                                </span>

                            </a>

                        <?php endif; ?>

                    </li>

                </ul>

            </nav>

        </div>

    </div>

</header>


<main id="content">

    <div class="page-content">
