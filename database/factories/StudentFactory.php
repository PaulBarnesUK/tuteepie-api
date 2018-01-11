<?php

use Faker\Generator as Faker;
use App\Student;
use App\Tutor;
use App\User;
use App\Location;

$factory->define(Student::class, function (Faker $faker) {
    $tutors = Tutor::all();
    $user = factory(User::class)->create();
    $testPostcodes = config('testdata.postcodes');
    $key = rand(1, count($testPostcodes));

    $location = factory(Location::class)->create(['user_id' => $user->id, 'postcode' => $testPostcodes[$key]]);

    return [
        'user_id' => $user->id,
        'tutor_id' => $tutors->random()->id,
        'default_collection_location' => $location->id,
        'default_drop_off_location' => $location->id
    ];
});
