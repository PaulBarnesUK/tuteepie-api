<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'tutor_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function tutor()
    {
        return $this->belongsTo('App\Tutor');
    }

    public function defaultCollectionLocation()
    {
        return $this->hasOne('App\Location', 'id', 'default_collection_location');
    }

    public function defaultDropOffLocation()
    {
        return $this->hasOne('App\Location', 'id', 'default_drop_off_location');
    }
}
