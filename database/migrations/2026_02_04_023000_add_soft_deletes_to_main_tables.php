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
        if (!Schema::hasColumn('usuarios', 'deleted_at')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('pacientes', 'deleted_at')) {
            Schema::table('pacientes', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('turnos', 'deleted_at')) {
            Schema::table('turnos', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('turnos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
