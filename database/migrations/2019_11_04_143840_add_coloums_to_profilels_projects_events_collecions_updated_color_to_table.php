<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumsToProfilelsProjectsEventsCollecionsUpdatedColorToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('updated_color')->default(0);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->integer('updated_color')->default(0);
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->integer('updated_color')->default(0);
        });

        Schema::table('collections', function (Blueprint $table) {
            $table->integer('updated_color')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
