<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLikeUsersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'like_users';

    /**
     * Run the migrations.
     * @table like_users
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('liked_by_user_id')->unsigned()->index();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->integer('item_type')->comment('0=project 1=event 2=collection');
            $table->string('item_id', 45);
            $table->dateTime('liked_date');
            $table->timestamps();


            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('no action');

            $table->foreign('liked_by_user_id')
                ->references('id')->on('users')
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
       Schema::dropIfExists($this->tableName);
     }
}
