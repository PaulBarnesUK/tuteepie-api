<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Tutor;
use Illuminate\Support\Facades\Config;

class TutorCanCreateStudentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test()
    {
        // Seed database
        $this->artisan('db:seed');
        // Find a tutor in database
        $tutor = Tutor::all()->first();

        // Authenticate as the tutor
        $authResponse = $this->json('POST', "/api/{$this->apiVersion}/auth", [
            'email' => $tutor->user->email,
            'password' => 'secret'
        ])->assertSuccessful();

        $authHeader = "Bearer {$authResponse->baseResponse->original['token']}";

        // Call to API to create student
        // Assert that it returns the current student with the linked user
        $createUserResponse = $this->withHeaders([
            'Authorization' => $authHeader
        ])->json('POST', "/api/{$this->apiVersion}/tutors/{$tutor->id}/students", [
            'name' => 'Jim Smith',
            'email' => 'jimsmith@example.com'
        ])->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes'
            ]
        ]);

        // Assert that a user was created for the student
        $this->assertDatabaseHas('users', [
            'email' => 'jimsmith@example.com'
        ]);

        // Assert that the student was created
        $this->assertDatabaseHas('students', [
            'tutor_id' => $tutor->id
        ]);
    }
}
