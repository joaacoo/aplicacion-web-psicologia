<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $count = \Illuminate\Support\Facades\DB::table('sesiones')->count();
    echo "Current active sessions in 'sesiones': " . $count . "\n";
} catch (\Exception $e) {
    echo "Error querying sessions: " . $e->getMessage() . "\n";
}
