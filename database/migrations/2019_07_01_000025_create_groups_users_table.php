<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsUsersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'groups_users';

    /**
     * Run the migrations.
     * @table groups_users
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('groups_id')->unsigned()->index();
            $table->bigInteger('users_id')->unsigned()->index();
            $table->timestamps();


            $table->foreign('groups_id')
                ->references('id')->on('groups')
                ->onDelete('no action');

            $table->foreign('users_id')
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
