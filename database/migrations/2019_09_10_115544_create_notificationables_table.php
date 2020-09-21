<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notificationables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('notification_id')->unsigned()->index();
            $table->bigInteger('notificationable_id')->unsigned()->index();
            $table->string('notificationable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notificationables');
    }
}
