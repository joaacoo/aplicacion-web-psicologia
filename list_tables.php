<?php
$dbPath = __DIR__ . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'database.sqlite';
try {
    $pdo = new PDO("sqlite:" . $dbPath);
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_ASSOC);
    echo "TOTAL TABLES: " . count($tables) . "\n";
    foreach ($tables as $table) {
        echo "- " . $table['name'] . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
