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
        DB::statement("ALTER TABLE turnos MODIFY COLUMN estado ENUM('pendiente', 'confirmado', 'asistido', 'cancelado', 'completado', 'sesion_perdida') NOT NULL DEFAULT 'pendiente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE turnos MODIFY COLUMN estado ENUM('pendiente', 'confirmado', 'asistido', 'cancelado', 'completado') NOT NULL DEFAULT 'pendiente'");
    }
};
