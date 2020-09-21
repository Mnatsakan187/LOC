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

$factory->define(User::class, function (Faker $faker) {
    return [
        'firstName'                 => $faker->name,
        'lastName'                  => $faker->name,
        'email'                     => $faker->unique()->safeEmail,
        'accountType'               => 0,
        'password'                  => 123456,
        'password_confirmation'     => 123456,
        'preferredPronoun'          => $faker->name,
        'isVerified'                => 1,
        'streetAddress'             => $faker->name,
        'dateOfBirth'               => date('d-m-Y'),
        'location'                  => $faker->longitude . ' ' . $faker->latitude,
        'contentPreferenceWritten'  => $faker->numberBetween(0,1),
        'contentPreferenceAudio'    => $faker->numberBetween(0,1),
        'contentPreferenceVisual'   => $faker->numberBetween(0,1),
        'contentPreferenceEvents'   => $faker->numberBetween(0,1),
        'subscriptionId'            => $faker->numberBetween(0,1),
        'avatarUri'                 => $faker->name,
        'backgroundUri'             => $faker->name,
    ];
});
