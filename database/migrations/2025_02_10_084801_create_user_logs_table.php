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
        Schema::connection('sqlite')->create('user_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->json('request')->nullable();
            $table->json('response')->nullable();
            $table->integer('user_id')->unsigned();
            $table->integer('token_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sqlite')->dropIfExists('user_logs');
    }
};
