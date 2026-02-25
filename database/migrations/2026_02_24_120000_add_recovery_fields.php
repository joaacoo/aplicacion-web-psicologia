<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            if (!Schema::hasColumn('turnos', 'credito_id')) {
                $table->unsignedBigInteger('credito_id')->nullable()->after('estado_pago');
                $table->foreign('credito_id')->references('id')->on('patient_credits')->onDelete('set null');
            }
            if (!Schema::hasColumn('turnos', 'es_sesion_recuperacion')) {
                $table->boolean('es_sesion_recuperacion')->default(false)->after('credito_id');
            }
        });

        Schema::table('waitlists', function (Blueprint $table) {
            if (!Schema::hasColumn('waitlists', 'is_recovery')) {
                $table->boolean('is_recovery')->default(false)->after('hora_inicio');
            }
            if (!Schema::hasColumn('waitlists', 'original_appointment_id')) {
                $table->unsignedBigInteger('original_appointment_id')->nullable()->after('usuario_id');
            }
        });

        Schema::table('patient_credits', function (Blueprint $table) {
            if (!Schema::hasColumn('patient_credits', 'used_in_appointment_id')) {
                $table->unsignedBigInteger('used_in_appointment_id')->nullable()->after('appointment_id');
                $table->foreign('used_in_appointment_id')->references('id')->on('turnos')->onDelete('set null');
            }
            if (!Schema::hasColumn('patient_credits', 'used_at')) {
                $table->timestamp('used_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            $table->dropForeign(['credito_id']);
            $table->dropColumn(['credito_id', 'es_sesion_recuperacion']);
        });

        Schema::table('waitlists', function (Blueprint $table) {
            $table->dropColumn(['is_recovery', 'original_appointment_id']);
        });

        Schema::table('patient_credits', function (Blueprint $table) {
            $table->dropForeign(['used_in_appointment_id']);
            $table->dropColumn(['used_in_appointment_id', 'used_at']);
        });
    }
};
