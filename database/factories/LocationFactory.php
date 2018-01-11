<?php

use Faker\Generator as Faker;
use App\Location;
use App\User;

$factory->define(Location::class, function (Faker $faker) {
    $users = User::all();
    $testPostcodes = config('testdata.postcodes');
    $key = rand(1, count($testPostcodes));

    return [
        'user_id' => $users->random()->id,
        'postcode' => $testPostcodes[$key]
    ];
});
