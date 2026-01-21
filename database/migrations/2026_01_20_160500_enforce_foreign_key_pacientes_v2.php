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
        // 1. Clean up orphaned patients (patients with user_id that doesn't exist in usuarios)
        $orphans = DB::table('pacientes')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('usuarios')
                      ->whereRaw('pacientes.user_id = usuarios.id');
            })
            ->delete();

        if ($orphans > 0) {
            \Illuminate\Support\Facades\Log::info("EnforceFK: Deleted $orphans orphaned patient records.");
        }

        // 2. Add Foreign Key Constraint
        Schema::table('pacientes', function (Blueprint $table) {
            // Ensure the column is unsigned big integer (should be already, but safety first)
            // $table->unsignedBigInteger('user_id')->change(); 
            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('usuarios')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
