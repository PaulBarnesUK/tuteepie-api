<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Tutor;

class TutorCanCreateLessonTest extends TestCase
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

        // Authenticate as the tutor
        $authResponse = $this->json('POST', "/api/{$this->apiVersion}/auth", [
            'email' => $tutor->user->email,
            'password' => 'secret'
        ])->assertSuccessful();

        $authHeader = "Bearer {$authResponse->baseResponse->original['token']}";

        $this->withHeaders(['Authorization' => $authHeader])
            ->json('POST', "api/{$this->apiVersion}/tutors/{$tutor->id}/lessons", [
                'student_id' => $student->id,
                'start_time' => Carbon::now()->addDays(3)->toDateTimeString(),
                'end_time' => Carbon::now()->addDays(3)->addMinutes(120)->toDateTimeString(),
                'collection_location_id' => $student->defaultCollectionLocation->id,
                'drop_off_location_id' => $student->defaultDropOffLocation->id
            ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes'
                ]
            ]);
    }
}
