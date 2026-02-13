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
        Schema::table('activity_logs', function (Blueprint $table) {
            // Add polymorphic columns for tracking any model
            if (!Schema::hasColumn('activity_logs', 'model_type')) {
                $table->string('model_type')->nullable()->after('action');
            }
            if (!Schema::hasColumn('activity_logs', 'model_id')) {
                $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
            }
            if (!Schema::hasColumn('activity_logs', 'changes')) {
                $table->json('changes')->nullable()->after('model_id');
            }
            
            // Add indices for performance
            if (!Schema::hasIndex('activity_logs', 'activity_logs_model_type_model_id_index')) {
                $table->index(['model_type', 'model_id']);
            }
            if (!Schema::hasIndex('activity_logs', 'activity_logs_created_at_index')) {
                $table->index('created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['model_type', 'model_id']);
            $table->dropIndex(['created_at']);
            $table->dropColumn(['model_type', 'model_id', 'changes']);
        });
    }
};
