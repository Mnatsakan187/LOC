<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToProjectsPollsPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('pin_to_top')->default(0);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->integer('pin_to_top')->default(0);
        });

        Schema::table('polls', function (Blueprint $table) {
            $table->integer('pin_to_top')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects_polls_posts', function (Blueprint $table) {
            //
        });
    }
}
