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
        // 1. Create Settings Table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'precio_base_sesion'
            $table->string('value')->nullable();
            $table->timestamps();
        });

        // 2. Add 'precio_personalizado' to Pacientes table
        Schema::table('pacientes', function (Blueprint $table) {
            // Nullable: Null means use default/base price from settings
            $table->decimal('precio_personalizado', 10, 2)->nullable()->after('telefono');
            
            // Note: honorario_pactado was added previously but default 0. 
            // We can migrate that data if needed, or keep it as legacy.
            // For now, we add the strictly requested column.
        });

        // Seed initial default price
        \DB::table('settings')->insert([
            'key' => 'precio_base_sesion',
            'value' => '25000', // Default initial value
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');

        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn('precio_personalizado');
        });
    }
};
