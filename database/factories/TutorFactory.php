<?php

use Faker\Generator as Faker;
use App\User;
use App\Tutor;

$factory->define(Tutor::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->create()->id
    ];
});
