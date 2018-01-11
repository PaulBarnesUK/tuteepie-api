<?php

namespace Tests\Unit;

use App\Student;
use App\Tutor;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TutorCanDeleteStudentTest extends TestCase
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

        // Authenticate as the tutor
        $authResponse = $this->json('POST', "/api/{$this->apiVersion}/auth", [
            'email' => $tutor->user->email,
            'password' => 'secret'
        ])->assertSuccessful();

        $authHeader = "Bearer {$authResponse->baseResponse->original['token']}";

        // Select the student to "delete"
        $students = $tutor->students;
        $student = $students->first();

        $this->withHeaders(['Authorization' => $authHeader])
            ->json('DELETE', "/api/{$this->apiVersion}/tutors/{$tutor->id}/students/{$student->id}")
            ->assertSuccessful();

        $student = Student::find($student->id);
        $this->assertNull($student->tutor_id);
    }
}
