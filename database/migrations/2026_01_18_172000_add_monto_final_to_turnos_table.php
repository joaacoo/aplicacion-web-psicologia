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
        if (Schema::hasTable('turnos') && !Schema::hasColumn('turnos', 'monto_final')) {
            Schema::table('turnos', function (Blueprint $table) {
                $table->decimal('monto_final', 10, 2)->nullable()->after('estado');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('turnos') && Schema::hasColumn('turnos', 'monto_final')) {
            Schema::table('turnos', function (Blueprint $table) {
                $table->dropColumn('monto_final');
            });
        }
    }
};
