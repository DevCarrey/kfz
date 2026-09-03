<?php
declare(strict_types=1);

/*
 * Lokale Zugangsdaten gehören in database.local.php. Diese Datei wird durch
 * .gitignore nicht versioniert. Ohne Override bleiben die sicheren XAMPP-
 * Entwicklungsstandardwerte aktiv.
 */
$localConfigFile = __DIR__ . '/database.local.php';
$localConfig = is_file($localConfigFile)
    ? require $localConfigFile
    : [];

if (!is_array($localConfig)) {
    $localConfig = [];
}

$databaseHost = (string)($localConfig['host'] ?? '127.0.0.1');
$databaseName = (string)($localConfig['name'] ?? 'kfz_digital');
$databaseUser = (string)($localConfig['user'] ?? 'root');
$databasePassword = (string)($localConfig['password'] ?? '');
$databaseCharset = (string)($localConfig['charset'] ?? 'utf8mb4');

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
