<?php

namespace App\Http\Controllers;

use App\Lesson;
use App\Location;
use App\Transformers\LessonTransformer;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Tutor;
use App\User;
use App\Student;
use App\Transformers\StudentTransformer;
use Illuminate\Support\Facades\Config;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\JsonApiSerializer;
use Spatie\Fractal\Fractal;

class TutorController extends Controller
{
    use JsonableTrait;

    /**
     * Create a new student, associated with this tutor.
     *
     * @param Tutor $tutor
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createStudent(Tutor $tutor, Request $request)
    {
        $user = new User();

        if (!$user->validate($request->all(), $user->creationRules))
            return response()->json([
                'errors' => $user->errors
            ], 422);

        $createdUser = $user->create($request->all());

        $student = Student::create([
            'user_id' => $createdUser->id,
            'tutor_id' => $tutor->id
        ]);

        if ($student)
            return fractal()->item($student, new StudentTransformer(), 'students')
                ->respond(200);

        return response()->json([
            'message' => 'Unable to create student.'
        ], 500);
    }

    /**
     * Get a given student assigned to the tutor
     *
     * @param Tutor $tutor
     * @param Student $student
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudent(Tutor $tutor, Student $student)
    {
        return fractal()->item($student, new StudentTransformer(), 'students')
            ->respond(200);
    }

    /**
     * Delete the link between the tutor and the student
     * Sets the students 'tutor_id' column to null
     *
     * @param Tutor $tutor
     * @param Student $student
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteStudent(Tutor $tutor, Student $student)
    {
        if ($student->update(['tutor_id' => null]))
            return fractal()->item($student, new StudentTransformer(), 'students')
                ->respond(200);

        return $this->errorResponse(500, Config::get('constants.response_titles.GENERAL_ERROR'));
    }

    /**
     * Create a lesson for this tutor.
     *
     * @param Tutor $tutor
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function createLesson(Tutor $tutor, Request $request)
    {
        $data = $request->all();
        $data['tutor_id'] = $tutor->id;
        $tutorLessons = $tutor->lessons;
        $lesson = new Lesson();

        // Basic validation of request data
        if (!$lesson->validate($data, $lesson->creationRules))
            return $lesson->validationErrorResponse();

        // Calculate the duration of the lesson
        // TODO: Add in unit test to ensure duration is within tutor's accepted lesson durations, maybe via custom validation rule?
        $lessonStartTime = Carbon::parse($request->start_time);
        $lessonEndTime = Carbon::parse($request->end_time);
        $difference = $lessonStartTime->diffInMinutes($lessonEndTime);
        $data['duration'] = $difference;

        // Check that this lesson does not overlap with another of the tutor's lessons
        if (!$this->checkLessonAvailable($lessonStartTime, $lessonEndTime, $tutorLessons))
            return $this->errorResponse(403, config('constants.response_titles.VALIDATION_ERROR'), 'Lesson time unavailable.');

        // TODO: Check that tutor can get to this lesson from the last lesson in time.
        if (!$this->checkSufficientTravelTime($tutor, $lessonStartTime, Location::find($request->collection_location_id)))
            return $this->errorResponse(403, config('constants.response_titles.VALIDATION_ERROR'), 'Not enough travel time.');

        $lesson = $lesson->create($data);

        if ($lesson)
            return fractal()->item($lesson, new LessonTransformer(), 'lessons')
                ->respond(201);

        return $this->errorResponse(500, Config::get('constants.response_titles.GENERAL_ERROR'));
    }

    /**
     * Check that the requested lesson's start and end times do not intrude upon an existing lesson
     *
     * @param $requestedLessonStartTime
     * @param $requestedLessonEndTime
     * @param $bookedLessons
     * @return bool
     */
    protected function checkLessonAvailable(Carbon $requestedLessonStartTime, Carbon $requestedLessonEndTime, $bookedLessons)
    {
        foreach ($bookedLessons as $bookedLesson) {
            if ($requestedLessonStartTime->between($bookedLesson->start_time, $bookedLesson->end_time))
                return false;

            if ($requestedLessonEndTime->between($bookedLesson->start_time, $bookedLesson->end_time))
                return false;
        }

        return true;
    }

    protected function checkSufficientTravelTime(Tutor $tutor, Carbon $lessonStartTime, Location $collectionLocation)
    {
        $previousLesson = $tutor->lessons()
            ->whereDate('start_time', '<', $lessonStartTime->toDateTimeString())
            ->orderBy('start_time', 'desc')
            ->first();

        if (!$previousLesson)
            return true;

        $difference = $previousLesson->end_time->diffInSeconds($lessonStartTime);

        $apiKey = env('GOOGLE_DISTANCE_MATRIX_API_KEY');
        $client = new Client();
        $response = $client->get(
            "https://maps.googleapis.com/maps/api/distancematrix/json?key={$apiKey}&origins={$previousLesson->dropOffLocation->postcode}&destinations={$collectionLocation->postcode}&departure_time={$previousLesson->end_time->timestamp}"
        );
        $jsonResponse = json_decode($response->getBody()->getContents());

        if ($jsonResponse->rows[0]->elements[0]->status == 'OK') {
            $estimatedTravelTime = $jsonResponse->rows[0]->elements[0]->duration_in_traffic->value;
            $estimatedTravelTime += config('travel_seconds_buffer');

            return $estimatedTravelTime > $difference ? false : true;
        } else {
            return false;
        }
    }
}
