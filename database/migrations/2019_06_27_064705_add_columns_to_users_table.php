<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'first_name');
            $table->string('last_name', 45);
            $table->string('street_address', 45)->nullable();
            $table->integer('account_type')->comment('0= user, 1= creator');
            $table->integer('updated_by')->nullable();
            $table->string('display_name', 45);
            $table->string('creative_title', 45)->nullable();
            $table->dateTime('date_of_birth')->nullable();
            $table->string('location')->nullable();
            $table->integer('content_preference_written')->default(0);
            $table->integer('content_preference_audio')->default(0);
            $table->integer('content_preference_visual')->default(0);
            $table->integer('content_preference_events')->default(0);
            $table->integer('subscription_id')->default(0)->comment('0: free, 1: creator / professional plan');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
