<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $holidays = [
            ['date' => '2026-01-01', 'reason' => 'Año Nuevo'],
            ['date' => '2026-02-16', 'reason' => 'Carnaval'],
            ['date' => '2026-02-17', 'reason' => 'Carnaval'],
            ['date' => '2026-03-24', 'reason' => 'Día de la Memoria'],
            ['date' => '2026-04-02', 'reason' => 'Día del Veterano'],
            ['date' => '2026-04-03', 'reason' => 'Viernes Santo'],
            ['date' => '2026-05-01', 'reason' => 'Día del Trabajador'],
            ['date' => '2026-05-25', 'reason' => 'Día de la Revolución de Mayo'],
            ['date' => '2026-06-17', 'reason' => 'Paso a la Inmortalidad de Güemes'],
            ['date' => '2026-06-20', 'reason' => 'Paso a la Inmortalidad de Belgrano'],
            ['date' => '2026-07-09', 'reason' => 'Día de la Independencia'],
            ['date' => '2026-08-17', 'reason' => 'Paso a la Inmortalidad de San Martín'],
            ['date' => '2026-10-12', 'reason' => 'Día del Respeto a la Diversidad Cultural'],
            ['date' => '2026-11-20', 'reason' => 'Día de la Soberanía Nacional'],
            ['date' => '2026-12-08', 'reason' => 'Inmaculada Concepción'],
            ['date' => '2026-12-25', 'reason' => 'Navidad'],
        ];

        foreach ($holidays as $holiday) {
            \App\Models\BlockedDay::firstOrCreate(
                ['date' => $holiday['date']],
                ['reason' => $holiday['reason']]
            );
        }
    }
}
