<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Message::class, function (Faker $faker) {
    return [
        'fromUserId' =>  $this->faker->numberBetween(10,1000),
        'toUserId'   =>  $this->faker->numberBetween(1,10),
        'message'    =>  $this->faker->text($maxNbChars = 20),
        'summary'    =>  $this->faker->text($maxNbChars = 20),
        'isRead'     =>  $this->faker->numberBetween(0, 1),
    ];
});
