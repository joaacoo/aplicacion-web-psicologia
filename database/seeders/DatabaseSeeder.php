<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'nombre' => 'Admin Nazarena',
            'email' => 'admin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'rol' => 'admin',
        ]);

        // Sample Patient 1 (the one you used)
        User::create([
            'nombre' => 'Juan Paciente',
            'email' => 'test@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'rol' => 'paciente',
        ]);

        // More sample patients
        $maria = User::create([
            'nombre' => 'Maria Lopez',
            'email' => 'maria@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'rol' => 'paciente',
        ]);

        // Sample Appointment
        Appointment::create([
            'usuario_id' => $maria->id,
            'fecha_hora' => now()->addDays(2)->setHour(10)->setMinute(0),
            'estado' => 'pendiente',
            'notas' => 'Consulta inicial',
        ]);
    }
}
