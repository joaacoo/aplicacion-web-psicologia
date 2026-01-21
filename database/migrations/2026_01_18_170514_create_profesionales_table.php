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
        if (!Schema::hasTable('profesionales')) {
            Schema::create('profesionales', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->string('google_calendar_url')->nullable();
                $table->string('ical_token')->nullable();
                $table->integer('duracion_sesion')->default(45);
                $table->integer('intervalo_sesion')->default(15);
                $table->timestamps();
                
                // Foreign key
                // $table->foreign('user_id')->references('id')->on('usuarios')->onDelete('cascade');
            });
        }

        // Data Migration: Move data from users to profesionales
        $admins = \DB::table('usuarios')->where('rol', 'admin')->get();
        foreach ($admins as $admin) {
            \DB::table('profesionales')->updateOrInsert(
                ['user_id' => $admin->id],
                [
                    'google_calendar_url' => $admin->google_calendar_url ?? null,
                    'ical_token' => $admin->ical_token ?? null,
                    'duracion_sesion' => $admin->duracion_sesion ?? 45,
                    'intervalo_sesion' => $admin->intervalo_sesion ?? 15,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profesionales');
    }
};
