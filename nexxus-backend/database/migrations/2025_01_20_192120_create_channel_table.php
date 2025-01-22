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
        Schema::create('channel', function (Blueprint $table) {
            $table->id();
            $table->string('name_channel');
            $table->enum('type_channel', ['text', 'voice']);
            $table->foreignId('id_server');
            $table->foreign('id_server')->references('id')->on('server');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel');
    }
};
