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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_group')->default(false); // false = chat privado, true = chat grupal/servidor
            $table->boolean('is_server')->default(false); // true si es un "servidor" estilo Discord
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // Quién creó el chat
            $table->string('name')->nullable(); // Para nombres de servidores o grupos
            $table->foreignId('parent_id')->nullable()->constrained('chats')->onDelete('cascade'); // Subchats dentro de servidores
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
