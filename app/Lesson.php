<?php

namespace App;

use App\Http\Controllers\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use ValidatableTrait;

    protected $fillable = ['tutor_id', 'student_id', 'start_time', 'end_time', 'duration', 'collection_location_id', 'drop_off_location_id'];

    protected $dates = ['start_time', 'end_time'];

    public $creationRules = [
        'tutor_id' => 'required|exists:tutors,id',
        'student_id' => 'required|exists:students,id',
        'start_time' => 'required|date',
        'end_time' => 'required|date',
        'collection_location_id' => 'required|exists:locations,id',
        'drop_off_location_id' => 'required|exists:locations,id'
    ];

    public function student()
    {
        return $this->belongsTo('App\Student');
    }

    public function tutor()
    {
        return $this->belongsTo('App\Tutor');
    }

    public function dropOffLocation()
    {
        return $this->hasOne('App\Location', 'id', 'drop_off_location_id');
    }

    public function collectionLocation()
    {
        return $this->hasOne('App\Location', 'id', 'collection_location_id');
    }
}
