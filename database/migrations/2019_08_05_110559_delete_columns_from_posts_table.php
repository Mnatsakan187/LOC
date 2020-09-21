<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteColumnsFromPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('item_id');
            $table->dropColumn('item_type');
            $table->dropColumn('background_image_id');
            $table->dropColumn('profile_image_id');
            $table->bigInteger('profile_id')->unsigned()->index();
            $table->bigInteger('group_id')->unsigned()->index()->nullable();
            $table->string('image_uri')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            //
        });
    }
}
