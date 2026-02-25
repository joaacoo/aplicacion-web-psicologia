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
            $table->boolean('es_recuperacion')->default(false)->after('es_recurrente');
            $table->unsignedBigInteger('waitlist_id')->nullable()->after('es_recuperacion');
            
            // Add foreign key if possible, but let's keep it simple for now to avoid migration order issues
            // $table->foreign('waitlist_id')->references('id')->on('waitlists')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            $table->dropColumn(['es_recuperacion', 'waitlist_id']);
        });
    }
};
