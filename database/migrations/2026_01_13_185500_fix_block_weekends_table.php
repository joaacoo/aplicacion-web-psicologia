<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add to the correct table
        if (!Schema::hasColumn('usuarios', 'block_weekends')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->boolean('block_weekends')->default(false)->after('password');
            });
        }

        // Cleanup the wrong table if it was created (optional but cleaner)
        if (Schema::hasTable('users')) {
            // Check if it's the default Laravel one or the one we created by accident
            // If it has ONLY block_weekends or generic fields, we can drop it if it's not being used.
            // But to be safe, we just leave it or rename it if needed. 
            // For now, let's just make sure 'usuarios' is correct.
        }
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('block_weekends');
        });
    }
};
