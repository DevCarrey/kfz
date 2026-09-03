<?php
declare(strict_types=1);

/**
 * Kfz Digital – zentrale PDO-Datenbankverbindung
 */

$databaseHost = '127.0.0.1';
$databaseName = 'kfz_digital';
$databaseUser = 'root';
$databasePassword = '';
$databaseCharset = 'utf8mb4';

$databaseDsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    $databaseHost,
    $databaseName,
    $databaseCharset
);

try {
    $databaseConnection = new PDO(
        $databaseDsn,
        $databaseUser,
        $databasePassword,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    return $databaseConnection;

} catch (PDOException $exception) {
    http_response_code(500);

    exit(
        'Die Verbindung zur Datenbank konnte nicht hergestellt werden.'
    );
}
