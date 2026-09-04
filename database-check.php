<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/src/Config/database.php';

$requiredTables = [
    'users',
    'vehicles',
    'applications',
    'application_status_history',
];

$existingTables = $pdo
    ->query('SHOW TABLES')
    ->fetchAll(PDO::FETCH_COLUMN);

foreach ($requiredTables as $table) {
    if (!in_array($table, $existingTables, true)) {
        echo "FEHLT: {$table}" . PHP_EOL;
    } else {
        echo "OK: {$table}" . PHP_EOL;
    }
}
