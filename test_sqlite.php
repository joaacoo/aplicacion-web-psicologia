<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$dbPath = __DIR__ . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'database.sqlite';
echo "Checking path: " . $dbPath . "\n";
if (!file_exists($dbPath)) {
    echo "File does not exist!\n";
    exit;
}

try {
    $dsn = "sqlite:" . $dbPath;
    $pdo = new PDO($dsn);
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_ASSOC);
    echo "TABLES FOUND (" . count($tables) . "):\n";
    foreach ($tables as $table) {
        echo "- " . $table['name'] . "\n";
    }
    if (empty($tables)) {
        echo "No tables found in SQLite.\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
