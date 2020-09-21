<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'messages';

    /**
     * Run the migrations.
     * @table messages
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('from_user_id')->unsigned()->index();
            $table->bigInteger('to_user_id')->unsigned()->index();
            $table->string('message');
            $table->string('summary', 100);
            $table->integer('is_read')->default(0);
            $table->timestamps();


            $table->foreign('from_user_id')
                ->references('id')->on('users')
                ->onDelete('no action');

            $table->foreign('to_user_id')
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
