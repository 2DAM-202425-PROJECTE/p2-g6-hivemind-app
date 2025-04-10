<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the old profile frame column
            $table->dropColumn('equipped_profile_frame_path');
            // Add the new profile theme column (stores a CSS class or color value)
            $table->string('equipped_profile_theme')->nullable()->after('equipped_profile_icon_path');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert the changes
            $table->string('equipped_profile_frame_path')->nullable();
            $table->dropColumn('equipped_profile_theme');
        });
    }
};
