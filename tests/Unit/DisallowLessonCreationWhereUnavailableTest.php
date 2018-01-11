<?php

namespace Tests\Unit;

use App\Lesson;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Tutor;

class DisallowLessonCreationWhereUnavailableTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->artisan('db:seed');

        // Select the tutor
        $tutors = Tutor::all();

        $tutor = $tutors->first(function ($tutor) {
            return $tutor->students->isNotEmpty() && $tutor->lessons->isNotEmpty();
        });
        $student = $tutor->students->first();
        $lesson = $tutor->lessons->random();

        // Authenticate as the tutor
        $authResponse = $this->json('POST', "/api/{$this->apiVersion}/auth", [
            'email' => $tutor->user->email,
            'password' => 'secret'
        ]);

        $authHeader = "Bearer {$authResponse->baseResponse->original['token']}";

        $this->withHeaders(['Authorization' => $authHeader])
            ->json('POST', "api/{$this->apiVersion}/tutors/{$tutor->id}/lessons", [
                'student_id' => $student->id,
                'start_time' => $lesson->start_time->toDateTimeString(),
                'end_time' => $lesson->end_time->toDateTimeString(),
                'collection_location_id' => $student->defaultCollectionLocation->id,
                'drop_off_location_id' => $student->defaultDropOffLocation->id
            ])
            ->assertStatus(403);
    }
}
