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
        if (!Schema::hasTable('pacientes')) {
            Schema::create('pacientes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->string('tipo_paciente')->default('nuevo'); // 'nuevo', 'frecuente'
                $table->decimal('honorario_pactado', 10, 2)->default(0); // [NEW] Finance requirement
                $table->timestamps();
                
                // Foreign key
                // $table->foreign('user_id')->references('id')->on('usuarios')->onDelete('cascade');
            });
        }

        // Data Migration
        $patients = \DB::table('usuarios')->where('rol', 'paciente')->get();
        foreach ($patients as $pt) {
            \DB::table('pacientes')->updateOrInsert(
                ['user_id' => $pt->id],
                [
                    'tipo_paciente' => $pt->tipo_paciente ?? 'nuevo',
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
        Schema::dropIfExists('pacientes');
    }
};
