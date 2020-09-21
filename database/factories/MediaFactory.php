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

$factory->define(\App\Media::class, function (Faker $faker) {
    return [
        'displayName'    => $faker->name,
        'fieldName'      => $faker->name,
        'uri'            => $faker->name,
        'createdBy'      => $faker->numberBetween(1, 10),
        'updatedBy'      => $faker->numberBetween(1, 10),
        'mediaType'      => $faker->numberBetween(1, 3),
    ];
});
