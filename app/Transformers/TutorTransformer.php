<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;
use App\Tutor;

class TutorTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'user'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Tutor $tutor)
    {
        return [
            'id' => $tutor->id
        ];
    }

    public function includeUser(Tutor $tutor)
    {
        return $this->item($tutor->user, new UserTransformer(), 'users');
    }

    public function includeStudent(Tutor $tutor)
    {
        return $this->item($tutor->student, new StudentTransformer(), 'students');
    }
}
