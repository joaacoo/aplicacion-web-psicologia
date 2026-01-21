<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Safety check: if table exists and column doesn't
        if (Schema::hasTable('pacientes') && !Schema::hasColumn('pacientes', 'honorario_pactado')) {
            Schema::table('pacientes', function (Blueprint $table) {
                $table->decimal('honorario_pactado', 10, 2)->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pacientes') && Schema::hasColumn('pacientes', 'honorario_pactado')) {
            Schema::table('pacientes', function (Blueprint $table) {
                $table->dropColumn('honorario_pactado');
            });
        }
    }
};
