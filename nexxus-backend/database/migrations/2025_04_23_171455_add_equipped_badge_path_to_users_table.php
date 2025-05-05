<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add equipped_badge_path as TEXT (for JSON storage) if it doesn't exist
            if (!Schema::hasColumn('users', 'equipped_badge_path')) {
                $table->text('equipped_badge_path')->nullable()->after('equipped_profile_font_path');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop equipped_badge_path if it exists
            if (Schema::hasColumn('users', 'equipped_badge_path')) {
                $table->dropColumn('equipped_badge_path');
            }
        });
    }
};
