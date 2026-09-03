<?php
declare(strict_types=1);

/** Gemeinsame, kontextunabhängige Helfer für Layouts und Seiten. */
function kfz_app_prefix(): string
{
    return rtrim((string)($GLOBALS['appPrefix'] ?? ''), '/');
}

function kfz_url(string $path): string
{
    $path = '/' . ltrim($path, '/');

    return kfz_app_prefix() . $path;
}

function kfz_asset_url(string $path): string
{
    return kfz_url('/public/' . ltrim($path, '/'));
}

function kfz_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function kfz_is_logged_in(): bool
{
    return (int)($_SESSION['user_id'] ?? 0) > 0;
}

function kfz_safe_include(string $file): void
{
    if (is_file($file)) {
        require $file;
        return;
    }

    http_response_code(500);
    echo '<!-- Datei nicht gefunden: ' . kfz_escape($file) . ' -->';
}
