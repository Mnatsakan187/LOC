<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'events';

    /**
     * Run the migrations.
     * @table events
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->index();
            $table->string('name', 45);
            $table->dateTime('date')->nullable();
            $table->string('duration_in_hours', 45)->nullable();
            $table->string('venue', 45)->nullable();
            $table->string('street_adress', 45)->nullable();
            $table->string('number', 45)->nullable();
            $table->string('postal_code', 45)->nullable();
            $table->string('city', 45)->nullable();
            $table->string('town', 45)->nullable();
            $table->string('country', 45)->nullable();
            $table->string('latitude', 45)->nullable();
            $table->string('longitud', 45)->nullable();
            $table->integer('is_published')->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->timestamps();

            $table->foreign('user_id')
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
