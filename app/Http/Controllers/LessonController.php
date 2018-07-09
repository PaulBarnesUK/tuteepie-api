<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;
use App\Transformers\LessonTransformer;

class LessonController extends Controller
{
    use JsonableTrait;

    public function list(Request $request)
    {
        $lessons = new Lesson;

        if ($request->wheres) {
            foreach ($request->wheres as $where) {
                $lessons = $lessons->where($where['column'], $where['operator'], $where['value']);
            }
        }

        return fractal()->collection($lessons->get(), new LessonTransformer(), 'lessons')
            ->respond(200);
    }

    public function get(Lesson $lesson)
    {
        return fractal()->item($lesson, new LessonTransformer(), 'lessons')
            ->respond(200);
    }
}
