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
            $table->foreignId('paciente_id')->nullable()->constrained('pacientes')->onDelete('set null');
        });

        // Backfill Logic
        $turnos = \Illuminate\Support\Facades\DB::table('turnos')->get();
        foreach ($turnos as $turno) {
            $paciente = \Illuminate\Support\Facades\DB::table('pacientes')
                ->where('user_id', $turno->usuario_id)
                ->first();
            
            if ($paciente) {
                \Illuminate\Support\Facades\DB::table('turnos')
                    ->where('id', $turno->id)
                    ->update(['paciente_id' => $paciente->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            $table->dropForeign(['paciente_id']);
            $table->dropColumn('paciente_id');
        });
    }
};
