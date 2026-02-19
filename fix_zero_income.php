<?php

use App\Models\Appointment;
use App\Models\Paciente;
use App\Models\Setting;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Fix existing confirmed appointments with 0 monto_final
$affected = 0;
$appointments = Appointment::where('estado', 'confirmado')
    ->where(function($q) {
        $q->whereNull('monto_final')->orWhere('monto_final', 0);
    })->get();

echo "Found " . $appointments->count() . " appointments to fix.\n";

$basePrice = Setting::get('precio_base_sesion', 25000);

foreach ($appointments as $appt) {
    $honorario = $basePrice;
    if ($appt->user && $appt->user->paciente) {
        $honorario = $appt->user->paciente->precio_sesion;
    }
    
    $appt->update(['monto_final' => $honorario]);
    echo "Fixed Appt #{$appt->id} - New monto: {$honorario}\n";
    $affected++;
}

echo "Done. Fixed {$affected} records.\n";
