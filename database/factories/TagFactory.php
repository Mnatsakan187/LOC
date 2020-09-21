<?php

use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\App\Tag::class, function (Faker $faker) {
    return [
        'name'           => $faker->name,
        'description'    => $faker->name,
        'rbgColorCode'   => $faker->name,
        'createdBy'      => $faker->numberBetween(1, 10),
    ];
});
