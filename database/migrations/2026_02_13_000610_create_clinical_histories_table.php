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
        Schema::create('clinical_histories', function (Blueprint $table) {
            $table->id();
            
            // Link to specific turno (appointment)
            $table->foreignId('turno_id')
                  ->constrained('turnos')
                  ->onDelete('cascade');
            
            // Link to patient (for faster queries)
            $table->foreignId('paciente_id')
                  ->constrained('pacientes')
                  ->onDelete('cascade');
            
            // Encrypted clinical note content
            $table->longText('content');
            
            $table->timestamps();
            
            // Indices for performance
            $table->index('turno_id');
            $table->index('paciente_id');
            
            // Ensure one note per turno (optional but recommended)
            $table->unique('turno_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_histories');
    }
};
