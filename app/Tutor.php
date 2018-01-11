<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function students()
    {
        return $this->hasMany('App\Student');
    }

    public function lessons()
    {
        return $this->hasMany('App\Lesson');
    }

    public function isCurrentUser()
    {
        if (auth()->user()->id === $this->user->id) {
            return true;
        }

        return false;
    }
}
