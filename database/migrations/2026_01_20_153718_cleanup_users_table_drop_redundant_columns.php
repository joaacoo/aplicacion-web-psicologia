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
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn([
                'telefono', 
                'tipo_paciente', 
                'ical_token', 
                'google_calendar_url', 
                'duracion_sesion', 
                'intervalo_sesion'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('telefono')->nullable();
            $table->enum('tipo_paciente', ['nuevo', 'frecuente'])->default('nuevo');
            $table->string('ical_token')->nullable()->unique();
            $table->string('google_calendar_url', 500)->nullable();
            $table->integer('duracion_sesion')->default(45);
            $table->integer('intervalo_sesion')->default(15);
        });
    }
};
