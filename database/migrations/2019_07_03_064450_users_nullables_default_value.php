<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersNullablesDefaultValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('content_preference_written')->default(0)->nullable(true)->change();
            $table->integer('content_preference_audio')->default(0)->nullable(true)->change();
            $table->integer('content_preference_visual')->default(0)->nullable(true)->change();
            $table->integer('content_preference_events')->default(0)->nullable(true)->change();
            $table->integer('subscription_id')->default(0)->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
