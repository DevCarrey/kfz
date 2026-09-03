<?php
declare(strict_types=1);

/*
 * Zentraler Session-Start für jeden Web-Einstiegspunkt.
 * Die sichere Kennzeichnung wird nur bei einer HTTPS-Verbindung gesetzt,
 * damit die lokale XAMPP-Entwicklung über http://localhost weiterhin läuft.
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = !empty($_SERVER['HTTPS'])
        && strtolower((string)$_SERVER['HTTPS']) !== 'off';

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}
