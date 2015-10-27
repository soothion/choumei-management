<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Manager::class, function ($faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'username' => str_random(10),
        'password' => str_random(10),
        'status' => 1,
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Role::class, function ($faker) {
    return [
    ];
});
