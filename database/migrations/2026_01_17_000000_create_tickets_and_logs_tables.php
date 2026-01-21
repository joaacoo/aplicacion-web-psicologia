<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tickets')) {
            Schema::create('tickets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                // $table->foreign('user_id')->references('id')->on('usuarios')->onDelete('cascade');
                $table->string('subject');
                $table->text('description');
                $table->string('status')->default('nuevo');
                $table->string('priority')->default('media');
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('system_logs')) {
            Schema::create('system_logs', function (Blueprint $table) {
                $table->id();
                $table->string('level')->default('info');
                $table->text('message');
                $table->json('context')->nullable();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('url')->nullable();
                $table->string('ip')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('system_logs');
        Schema::dropIfExists('tickets');
    }
};
