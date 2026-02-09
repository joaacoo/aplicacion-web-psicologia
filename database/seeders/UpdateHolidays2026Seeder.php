<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\BlockedDay;

class UpdateHolidays2026Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Truncate existing holidays to clean up
        DB::statement('TRUNCATE TABLE blocked_days');

        // 2. Insert Official 2026 Holidays
        $feriados = [
            ['date' => '2026-01-01', 'reason' => 'Año Nuevo'],
            ['date' => '2026-02-16', 'reason' => 'Carnaval'],
            ['date' => '2026-02-17', 'reason' => 'Carnaval'],
            ['date' => '2026-03-23', 'reason' => 'Puente turístico no laborable'],
            ['date' => '2026-03-24', 'reason' => 'Día Nacional de la Memoria por la Verdad y la Justicia'],
            ['date' => '2026-04-02', 'reason' => 'Día del Veterano y de los Caídos en la Guerra de Malvinas'],
            ['date' => '2026-04-03', 'reason' => 'Viernes Santo'],
            ['date' => '2026-05-01', 'reason' => 'Día del Trabajador'],
            ['date' => '2026-05-25', 'reason' => 'Día de la Revolución de Mayo'],
            ['date' => '2026-06-20', 'reason' => 'Paso a la Inmortalidad del Gral. Manuel Belgrano'],
            ['date' => '2026-07-09', 'reason' => 'Día de la Independencia'],
            ['date' => '2026-07-10', 'reason' => 'Puente turístico no laborable'],
            ['date' => '2026-08-17', 'reason' => 'Paso a la Inmortalidad del Gral. José de San Martín'],
            ['date' => '2026-10-12', 'reason' => 'Día del Respeto a la Diversidad Cultural'],
            ['date' => '2026-11-20', 'reason' => 'Día de la Soberanía Nacional'],
            ['date' => '2026-12-07', 'reason' => 'Puente turístico no laborable'],
            ['date' => '2026-12-08', 'reason' => 'Inmaculada Concepción de María'],
            ['date' => '2026-12-25', 'reason' => 'Navidad'],
        ];

        foreach ($feriados as $f) {
            BlockedDay::create($f);
        }

        $count = BlockedDay::count();
        $this->command->info("Se han insertado $count feriados para 2026.");
    }
}
