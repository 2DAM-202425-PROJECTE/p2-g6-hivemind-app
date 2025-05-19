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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained()->onDelete('cascade')->index();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->index();
            $table->text('content');
            $table->boolean('is_edited')->default(false); // Indica si el mensaje fue editado
            $table->timestamp('deleted_at')->nullable(); // Soft delete para mensajes eliminados
            $table->timestamp('read_at')->nullable(); // Fecha de lectura
            $table->boolean('is_read')->default(false)->after('is_edited');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
