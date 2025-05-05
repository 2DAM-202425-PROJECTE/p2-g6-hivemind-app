<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Change equipped_badge_path to ensure it's nullable
            $table->text('equipped_badge_path')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert to non-nullable (optional, adjust as needed)
            $table->text('equipped_badge_path')->nullable(false)->change();
        });
    }
};
