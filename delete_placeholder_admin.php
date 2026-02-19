<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$u = User::where('email', 'admin@example.com')->first();
if ($u) {
    $u->delete();
    echo "Placeholder admin deleted.\n";
} else {
    echo "Placeholder admin not found.\n";
}
