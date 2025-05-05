<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Conditionally drop equipped_badge_path if it exists
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'equipped_badge_path')) {
                $table->dropColumn('equipped_badge_path');
            }
        });

        // Create user_badges pivot table
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->boolean('equipped')->default(false);
            $table->timestamps();

            // Ensure a user can't have the same badge multiple times
            $table->unique(['user_id', 'item_id']);
        });
    }

    public function down()
    {
        // Drop user_badges table
        Schema::dropIfExists('user_badges');

        // Re-add equipped_badge_path to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('equipped_badge_path')->nullable()->after('equipped_profile_font_path');
        });
    }
};
