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
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('fecha');
            $table->string('categoria', 50); // Ej: Alquiler, Internet, Supervisión
            $table->string('descripcion', 255)->nullable();
            $table->decimal('monto', 10, 2);
            $table->boolean('es_recurrente')->default(0); // Para gastos fijos mensuales
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
