<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Profile::class, function (Faker $faker) {
    return [
        "creativeTitle"               => $this->faker->text($maxNbChars = 20),
        "biography"                   => $this->faker->text($maxNbChars = 45),
        "location"                    => $faker->latitude(-90, 90),
    ];
});
