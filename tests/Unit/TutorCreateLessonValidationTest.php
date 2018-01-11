<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Tutor;
use Carbon\Carbon;

class TutorCreateLessonValidationTest extends TestCase
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
            return $tutor->students->isNotEmpty();
        });
        $student = $tutor->students->first();

        // Authenticate as the tutor
        $authResponse = $this->json('POST', "/api/{$this->apiVersion}/auth", [
            'email' => $tutor->user->email,
            'password' => 'secret'
        ])->assertSuccessful();

        $authHeader = "Bearer {$authResponse->baseResponse->original['token']}";

        // Try with no student provided.
        $this->withHeaders(['Authorization' => $authHeader])
            ->json('POST', "api/{$this->apiVersion}/tutors/{$tutor->id}/lessons", [
                'start_time' => Carbon::now()->addDays(3)->toDateTimeString(),
                'end_time' => Carbon::now()->addDays(3)->addMinutes(120)->toDateTimeString(),
                'collection_location' => $student->defaultCollectionLocation->id,
                'drop_off_location' => $student->defaultDropOffLocation->id
            ])
            ->assertStatus(422);

        // Try with invalid student
        $this->withHeaders(['Authorization' => $authHeader])
            ->json('POST', "api/{$this->apiVersion}/tutors/{$tutor->id}/lessons", [
                'student_id' => 9999,
                'start_time' => Carbon::now()->addDays(3)->toDateTimeString(),
                'end_time' => Carbon::now()->addDays(3)->addMinutes(120)->toDateTimeString(),
                'collection_location' => $student->defaultCollectionLocation->id,
                'drop_off_location' => $student->defaultDropOffLocation->id
            ])
            ->assertStatus(422);

        // Try with invalid date
        $this->withHeaders(['Authorization' => $authHeader])
            ->json('POST', "api/{$this->apiVersion}/tutors/{$tutor->id}/lessons", [
                'student_id' => $student->id,
                'start_time' => 'invalid date',
                'end_time' => Carbon::now()->addDays(3)->addMinutes(120)->toDateTimeString(),
                'collection_location' => $student->defaultCollectionLocation->id,
                'drop_off_location' => $student->defaultDropOffLocation->id
            ])
            ->assertStatus(422);

        // Try with missing end date
        $this->withHeaders(['Authorization' => $authHeader])
            ->json('POST', "api/{$this->apiVersion}/tutors/{$tutor->id}/lessons", [
                'student_id' => $student->id,
                'start_time' => 'invalid date',
                'collection_location' => $student->defaultCollectionLocation->id,
                'drop_off_location' => $student->defaultDropOffLocation->id
            ])
            ->assertStatus(422);

        // Try with invalid collection location
        $this->withHeaders(['Authorization' => $authHeader])
            ->json('POST', "api/{$this->apiVersion}/tutors/{$tutor->id}/lessons", [
                'student_id' => $student->id,
                'start_time' => Carbon::now()->addDays(3)->toDateTimeString(),
                'end_time' => Carbon::now()->addDays(3)->addMinutes(120)->toDateTimeString(),
                'collection_location' => 999999,
                'drop_off_location' => $student->defaultDropOffLocation->id
            ])
            ->assertStatus(422);

        // Try with all correct
        $this->withHeaders(['Authorization' => $authHeader])
            ->json('POST', "api/{$this->apiVersion}/tutors/{$tutor->id}/lessons", [
                'student_id' => $student->id,
                'start_time' => Carbon::now()->addDays(3)->toDateTimeString(),
                'end_time' => Carbon::now()->addDays(3)->addMinutes(120)->toDateTimeString(),
                'collection_location_id' => $student->defaultCollectionLocation->id,
                'drop_off_location_id' => $student->defaultDropOffLocation->id
            ])
            ->assertSuccessful();
    }
}
