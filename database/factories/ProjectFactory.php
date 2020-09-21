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

$factory->define(\App\Project::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'isPublished'              => $faker->numberBetween(0, 1),
        'description'              => $faker->name,
        'type'                     => $faker->numberBetween(0, 1),
        'backgroundUri'            => $faker->name,
        'originalBackgroundUri'    => $faker->name,
    ];
});
