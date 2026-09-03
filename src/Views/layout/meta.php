<?php
declare(strict_types=1);

/*
 * Der Prefix wird durch den zentralen Router ermittelt, damit das Projekt
 * sowohl unter /kfz/ als auch in einer eigenen VirtualHost-Umgebung läuft.
 */
require_once __DIR__ . '/../../Support/view_helpers.php';

$localPrefix = kfz_app_prefix();

/**
 * Erstellt URLs für deine Website.
 */
$metaUrl = static function (string $path) use ($localPrefix): string {
    return $localPrefix . '/' . ltrim($path, '/');
};

/**
 * Sicheres Escaping.
 */
$metaEscape = static fn (string $value): string => kfz_escape($value);

/**
 * Seitenspezifische Werte.
 */
$pageTitle = $pageTitle
    ?? 'Kfz Digital';

$pageDescription = $pageDescription
    ?? 'Kfz Digital – Fahrzeugvorgänge einfach, sicher und digital verwalten.';

$canonicalPath = $canonicalPath ?? '/';

$canonicalUrl = 'http://localhost' . $metaUrl($canonicalPath);

$ogImage = $ogImage
    ?? '/public/assets/img/og-image.jpg';

$ogImageUrl = 'http://localhost' . $metaUrl($ogImage);

$pageType = $pageType ?? 'website';
?>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1">

<meta
    name="description"
    content="<?= $metaEscape($pageDescription) ?>">

<meta
    name="robots"
    content="index, follow">

<meta
    name="author"
    content="Kfz Digital">

<meta
    name="theme-color"
    content="#202522">

<meta
    name="color-scheme"
    content="light">

<title><?= $metaEscape($pageTitle) ?></title>

<!-- Canonical -->
<link
    rel="canonical"
    href="<?= $metaEscape($canonicalUrl) ?>">

<!-- Favicon -->
<link
    rel="icon"
    type="image/png"
    href="<?= $metaEscape($metaUrl('/public/assets/img/favicon.png')) ?>">

<!-- Bootstrap CSS -->
<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
    rel="stylesheet">

<!-- Deine CSS-Datei -->
<link
    rel="stylesheet"
    href="<?= $metaEscape($metaUrl('/public/assets/css/style.css')) ?>">

<!-- Open Graph -->
<meta
    property="og:type"
    content="<?= $metaEscape($pageType) ?>">

<meta
    property="og:title"
    content="<?= $metaEscape($pageTitle) ?>">

<meta
    property="og:description"
    content="<?= $metaEscape($pageDescription) ?>">

<meta
    property="og:url"
    content="<?= $metaEscape($canonicalUrl) ?>">

<meta
    property="og:site_name"
    content="Kfz Digital">

<meta
    property="og:locale"
    content="de_DE">

<meta
    property="og:image"
    content="<?= $metaEscape($ogImageUrl) ?>">

<!-- Twitter / X -->
<meta
    name="twitter:card"
    content="summary_large_image">

<meta
    name="twitter:title"
    content="<?= $metaEscape($pageTitle) ?>">

<meta
    name="twitter:description"
    content="<?= $metaEscape($pageDescription) ?>">

<meta
    name="twitter:image"
    content="<?= $metaEscape($ogImageUrl) ?>">

        <?php if (is_file(__DIR__ . '/../../../public/assets/img/LOGO.png')): ?>
        <link
            rel="icon"
            type="image/png"
            href="<?= $metaEscape($metaUrl('/public/assets/img/LOGO.png')) ?>"
        >
    <?php endif; ?>
