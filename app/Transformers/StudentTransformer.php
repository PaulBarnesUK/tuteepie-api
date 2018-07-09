<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Student;

class StudentTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'user',
        'defaultCollectionLocation'
    ];

    public function transform(Student $student)
    {
        return [
            'id' => $student->id
        ];
    }

    public function includeUser(Student $student)
    {
        return $this->item($student->user, new UserTransformer(), 'users');
    }

    public function includeTutor(Student $student)
    {
        return $this->item($student->tutor, new TutorTransformer(), 'tutors');
    }

    public function includeDefaultCollectionLocation(Student $student)
    {
        if ($student->defaultCollectionLocation)
            return $this->item($student->defaultCollectionLocation, new LocationTransformer(), 'locations');
    }
}
