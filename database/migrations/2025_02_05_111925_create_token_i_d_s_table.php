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
        Schema::create('token_i_d_s', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->uuid();

            $table->string('access_token', 28);

            $table->unsignedBigInteger('token_id');
            $table->foreign('token_id')
                    ->references('id')
                    ->on('personal_access_tokens')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_i_d_s');
    }
};
