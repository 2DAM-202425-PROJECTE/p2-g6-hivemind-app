<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follows', function (Blueprint $table) {
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('followed_id')->constrained('users')->onDelete('cascade');
            $table->primary(['follower_id', 'followed_id']);
            $table->index('followed_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
