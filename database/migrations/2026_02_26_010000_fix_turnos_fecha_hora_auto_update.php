<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix for MySQL/MariaDB where timestamp columns might have DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        // We want fecha_hora to be a fixed point in time, never automatically updated by the DB.
        Schema::table('turnos', function (Blueprint $table) {
            $table->dateTime('fecha_hora')->change(); // Using dateTime often avoids the 'on update' behavior of timestamp in some MySQL versions
        });
        
        // Ensure no default 'on update' remains
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE turnos MODIFY COLUMN fecha_hora DATETIME NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting to timestamp if necessary, but ideally we keep it as datetime to avoid the auto-update quirk
        Schema::table('turnos', function (Blueprint $table) {
            $table->timestamp('fecha_hora')->useCurrent()->useCurrentOnUpdate()->change();
        });
    }
};
