<?php

namespace Tests\Unit;

use App\Lesson;
use App\Location;
use App\Student;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Tutor;
use Carbon\Carbon;

class TutorCannotCreateLessonIfNotEnoughTravelTimeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $tutor = factory(Tutor::class)->create();
        $student = factory(Student::class)->create(['tutor_id' => $tutor->id]);
        $lessons = factory(Lesson::class, 20)->create(['tutor_id' => $tutor->id, 'student_id' => $student->id]);
        factory(Location::class, 20)->create();
        $lesson = $lessons->random();

        $location = Location::where('id', '!=', $lesson->drop_off_location_id)->first();

        Passport::actingAs($tutor->user);

        $response = $this->json('POST', "api/{$this->apiVersion}/tutors/{$tutor->id}/lessons", [
            'student_id' => $student->id,
            'start_time' => $lesson->end_time->addMinutes(1)->toDateTimeString(),
            'end_time' => $lesson->end_time->addMinutes(121)->toDateTimeString(),
            'collection_location_id' => $location->id,
            'drop_off_location_id' => $location->id
        ])->assertStatus(403);
    }
}
