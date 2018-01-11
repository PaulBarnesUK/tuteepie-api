<?php

namespace App\Http\Controllers;

use App\Student;
use Illuminate\Http\Request;
use App\Tutor;
use App\User;
use App\Transformers\StudentTransformer;

class StudentController extends Controller
{
    public function create(Tutor $tutor, Request $request)
    {
        $user = new User();

        if (!$user->validate($request->all(), $user->creationRules)) {
            return response()->json([
                'errors' => $user->errors
            ], 422);
        }

        $createdUser = $user->create($request->all());

        $student = Student::create([
            'user_id' => $createdUser->id,
            'tutor_id' => $tutor->id
        ]);

        if ($student) {
            return fractal($student, new StudentTransformer())->respond(201);
        } else {
            return response()->json([
                'message' => 'Unable to create student.'
            ], 500);
        }
    }
}
