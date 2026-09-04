<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Support/view_helpers.php';

$url = static fn (string $path): string => kfz_url($path);
$asset = static fn (string $path): string => kfz_asset_url($path);
$escape = static fn (mixed $value): string => kfz_escape($value);

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

$appPrefix = kfz_app_prefix();

$isActive = static function (
    string $path
) use (
    $appPrefix,
    $currentPath
): bool {
    $path = '/' . ltrim($path, '/');

    $fullPath = $appPrefix . $path;
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

            <a
                href="<?= $escape($url('/')) ?>"
                class="kfz-brand"
                aria-label="Kfz Digital – Startseite"
            >

                <?php if ($hasLogo): ?>

                    <img
                        src="<?= $escape(
                            $asset('/assets/img/LOGO.png')
                        ) ?>"
                        alt="Kfz Digital"
                        class="kfz-brand-logo"
                    >

                <?php else: ?>

                    <span
                        class="kfz-brand-icon"
                        aria-hidden="true"
                    >
                        <svg viewBox="0 0 64 64">
                            <path d="M13 38h38l-3-13a5 5 0 0 0-4.8-3.8H20.8A5 5 0 0 0 16 25L13 38Z"/>
                            <path d="M9 38h46v11H9z"/>
                            <circle cx="19" cy="49" r="5"/>
                            <circle cx="45" cy="49" r="5"/>
                            <path d="M17 30h30"/>
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


            <nav
                id="kfzMainNavigation"
                class="kfz-main-navigation"
                aria-label="Hauptnavigation"
            >

                <ul class="kfz-navigation-list">

                    <li class="kfz-navigation-item">
                        <a
                            href="<?= $escape($url('/')) ?>"
                            class="kfz-navigation-link <?= $isActive('/')
                                ? 'active'
                                : '' ?>"
                            <?= $isActive('/')
                                ? 'aria-current="page"'
                                : '' ?>
                        >
                            Startseite
                        </a>
                    </li>

                    <li class="kfz-navigation-item">
                        <a
                            href="<?= $escape(
                                $url('/vorgang-pruefen/')
                            ) ?>"
                            class="kfz-navigation-link <?= $isActive(
                                '/vorgang-pruefen/'
                            ) ? 'active' : '' ?>"
                        >
                            Vorgang prüfen
                        </a>
                    </li>

                    <li class="kfz-navigation-item">
                        <a
                            href="<?= $escape($url('/hilfe/')) ?>"
                            class="kfz-navigation-link <?= $isActive(
                                '/hilfe/'
                            ) ? 'active' : '' ?>"
                        >
                            Hilfe
                        </a>
                    </li>

                    <li class="kfz-navigation-item">
                        <a
                            href="<?= $escape($url('/faq/')) ?>"
                            class="kfz-navigation-link <?= $isActive(
                                '/faq/'
                            ) ? 'active' : '' ?>"
                        >
                            FAQ
                        </a>
                    </li>

                    <li class="kfz-navigation-item">
                        <a
                            href="<?= $escape($url('/kontakt/')) ?>"
                            class="kfz-navigation-link <?= $isActive(
                                '/kontakt/'
                            ) ? 'active' : '' ?>"
                        >
                            Kontakt
                        </a>
                    </li>

                    <li class="kfz-navigation-item kfz-navigation-action">
                        <a
                            href="#fahrzeug-abmelden"
                            class="kfz-button kfz-button-primary"
                        >
                            Fahrzeug abmelden
                        </a>
                    </li>

                </ul>

            </nav>

        </div>

    </div>

</header>

<main id="content">
    <div class="page-content">