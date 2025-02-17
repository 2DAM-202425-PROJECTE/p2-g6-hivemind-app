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
        Schema::create('chat_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained()->onDelete('cascade')->index();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->index();
            $table->enum('role', ['admin', 'moderator', 'member'])->default('member'); // Rol del usuario en el chat
            $table->timestamp('joined_at')->useCurrent(); // Fecha de ingreso al chat
            $table->boolean('is_muted')->default(false); // Indica si el usuario está silenciado
            $table->timestamps();

            $table->unique(['chat_id', 'user_id']); // Asegura que un usuario no se duplique en el mismo chat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_user');
    }
};
