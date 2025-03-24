<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationToPostsTable extends Migration
{
    public function up()
    {
        Schema::table('post', function (Blueprint $table) {
            $table->string('location')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('post', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }
}
