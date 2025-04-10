<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableForProfileEffects extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('equipped_profile_theme');
            $table->string('equipped_profile_effect')->nullable()->after('equipped_profile_font_path');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('equipped_profile_effect');
            $table->string('equipped_profile_theme')->nullable()->after('equipped_profile_font_path');
        });
    }
}
