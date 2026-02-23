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
        Schema::table('turnos', function (Blueprint $table) {
            if (!Schema::hasColumn('turnos', 'estado_realizado')) {
                $table->enum('estado_realizado', ['pendiente', 'realizado'])->default('pendiente');
            }
            if (!Schema::hasColumn('turnos', 'motivo_cancelacion')) {
                $table->string('motivo_cancelacion')->nullable();
            }
            if (!Schema::hasColumn('turnos', 'cancelado_por')) {
                $table->enum('cancelado_por', ['sistema', 'usuario', 'admin'])->default('sistema');
            }
            if (!Schema::hasColumn('turnos', 'estado_pago')) {
                $table->enum('estado_pago', ['pendiente', 'verificado'])->default('pendiente');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            //
        });
    }
};
