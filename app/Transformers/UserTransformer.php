<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\User;

class UserTransformer extends TransformerAbstract
{
    protected $dates = ['activated_at'];
    
    protected $availableIncludes = [
        'type'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'activated_at' => $user->activated_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at
        ];
    }

    public function includeType(User $user)
    {
        switch ($user->type()) {
            case 'student':
                return $this->item($user->student, new StudentTransformer(), 'student');
                break;
            case 'tutor':
                return $this->item($user->tutor, new TutorTransformer(), 'tutor');
                break;
        }
    }
}
