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
            $table->enum('modalidad', ['virtual', 'presencial'])->default('presencial')->after('fecha_hora');
            $table->string('link_reunion')->nullable()->after('modalidad');
            $table->timestamp('vence_en')->nullable()->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            $table->dropColumn(['modalidad', 'link_reunion', 'vence_en']);
        });
    }
};
