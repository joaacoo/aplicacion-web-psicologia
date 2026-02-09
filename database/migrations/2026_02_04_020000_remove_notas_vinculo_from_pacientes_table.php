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
        if (Schema::hasColumn('pacientes', 'notas_vinculo')) {
            Schema::table('pacientes', function (Blueprint $table) {
                $table->dropColumn('notas_vinculo');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('pacientes', 'notas_vinculo')) {
            Schema::table('pacientes', function (Blueprint $table) {
                $table->text('notas_vinculo')->nullable()->after('honorario_pactado');
            });
        }
    }
};
