<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Lesson;

class LessonTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'tutor',
        'student'
    ];

    public function transform(Lesson $lesson)
    {
        return [
            'id' => $lesson->id,
            'start_time' => $lesson->start_time,
            'end_time' => $lesson->end_time,
            'duration' => $lesson->duration
        ];
    }

    public function includeTutor(Lesson $lesson)
    {
        return $this->item($lesson->tutor, new TutorTransformer(), 'tutors');
    }

    public function includeStudent(Lesson $lesson)
    {
        return $this->item($lesson->student, new StudentTransformer(), 'students');
    }
}
