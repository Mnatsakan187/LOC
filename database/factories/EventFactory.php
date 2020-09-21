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

$factory->define(\App\Event::class, function (Faker $faker) {
    return [
        'name'             => $faker->name,
        'date'             => Carbon\Carbon::now()->toDateTimeString(),
        'durationInHours'  => $faker->name,
        'venue'            => $faker->name,
        'streetAdress'     => $faker->name,
        'number'           => $faker->name,
        'postalCode'       => $faker->name,
        'city'             => $faker->name,
        'description'      => $faker->name,
        'town'             => $faker->name,
        'country'          => $faker->name,
        'latitude'         => $faker->name,
        'longitud'         => $faker->name,
        'isPublished'      => $faker->numberBetween(0, 1),
        'categoryId'       => $faker->numberBetween(0, 1000),
        'cost'             => $faker->numberBetween(0, 1),
    ];
});
