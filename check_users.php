<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $users = App\Models\User::all();
    if ($users->isEmpty()) {
        echo "No existing users found.\n";
    } else {
        foreach ($users as $user) {
            echo "User found: ID: {$user->id}, Name: {$user->nombre}, Email: {$user->email}, Role: {$user->rol}\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
