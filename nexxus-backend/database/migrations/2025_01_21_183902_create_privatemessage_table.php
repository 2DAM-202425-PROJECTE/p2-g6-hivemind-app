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
        Schema::create('privatemessage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sender');
            $table->foreignId('id_receiver');
            $table->text('content');
            $table->date('send_date');
            $table->boolean('read_status');
            $table->foreign('id_sender')->references('id')->on('users');
            $table->foreign('id_receiver')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('privatemessage');
    }
};
