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
        Schema::create('post', function (Blueprint $table) {
            $table->id();
            $table->string('content');
            $table->string('description')->nullable();
            $table->date('publish_date');
            $table->foreignId('id_user');
            $table->string('profile_photo')->nullable();
            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('profile_photo')->references('profile_photo_path')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post');
    }
};
