<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Lesson;

class LessonTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Lesson $lesson)
    {
        return [
            'id' => $lesson->id,
            'start_time' => $lesson->start_time,
            'end_time' => $lesson->end_time,
            'duration' => $lesson->duration
        ];
    }
}
