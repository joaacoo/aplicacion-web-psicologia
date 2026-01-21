<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('telefono')->nullable()->after('user_id');
        });

        // Data Migration: Move telefono from users to pacientes
        // ONLY for users with role 'paciente'
        \DB::statement("
            UPDATE pacientes 
            JOIN usuarios ON pacientes.user_id = usuarios.id 
            SET pacientes.telefono = usuarios.telefono 
            WHERE usuarios.rol = 'paciente'
        ");
        
        // Also ensure Profesional data is synced for admins (just in case)
        $admins = \DB::table('usuarios')->where('rol', 'admin')->get();
        foreach ($admins as $admin) {
            \DB::table('profesionales')->updateOrInsert(
                ['user_id' => $admin->id],
                [
                    'ical_token' => $admin->ical_token ?? null,
                    'google_calendar_url' => $admin->google_calendar_url ?? null,
                    'duracion_sesion' => $admin->duracion_sesion ?? 45,
                    'intervalo_sesion' => $admin->intervalo_sesion ?? 15,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            //
        });
    }
};
