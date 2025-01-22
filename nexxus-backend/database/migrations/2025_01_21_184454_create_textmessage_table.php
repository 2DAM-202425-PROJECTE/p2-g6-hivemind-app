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
        Schema::create('textmessage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_channel');
            $table->foreignId('id_sender');
            $table->string('message');
            $table->foreign('id_channel')->references('id')->on('channel');
            $table->foreign('id_sender')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('textmessage');
    }
};
