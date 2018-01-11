<?php

use Faker\Generator as Faker;
use App\User;
use Carbon\Carbon;
use App\Lesson;
use App\Student;
use App\Tutor;

$factory->define(Lesson::class, function (Faker $faker) {
    $weeksToAdd = rand(1, 15);
    $duration = 120;

    $students = Student::all();
    $student = $students->random();

    return [
        'tutor_id' => $student->tutor->id,
        'student_id' => $student->id,
        'collection_location_id' => $student->defaultCollectionLocation->id,
        'drop_off_location_id' => $student->defaultDropOffLocation->id,
        'start_time' => Carbon::now()->addWeeks($weeksToAdd),
        'end_time' => Carbon::now()->addWeeks($weeksToAdd)->addMinutes($duration),
        'duration' => $duration
    ];
});
