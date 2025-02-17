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
        Schema::create('message_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade')->index(); // Relación con el mensaje original
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Quién hizo la edición/eliminación
            $table->text('old_content'); // Contenido antes de la edición/eliminación
            $table->enum('action', ['edited', 'deleted']); // Tipo de acción
            $table->timestamp('changed_at')->useCurrent(); // Fecha de la modificación
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_histories');
    }
};
