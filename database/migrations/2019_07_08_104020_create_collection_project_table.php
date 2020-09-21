<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_project', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('collection_id')->unsigned()->index();
            $table->bigInteger('project_id')->unsigned()->index();
            $table->timestamps();


            $table->foreign('collection_id')
                ->references('id')->on('collections')
                ->onDelete('no action');

            $table->foreign('project_id')
                ->references('id')->on('projects')
                ->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collection_project');
    }
}
