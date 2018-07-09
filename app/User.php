<?php

namespace App;

use App\Http\Controllers\ValidatableTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Validator;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, ValidatableTrait;

    protected $dates = ['activated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'activated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public $creationRules = [
        'name' => 'required|min:4',
        'email' => 'required|email|unique:users',
        'password' => 'min:8'
    ];

    public $updateRules = [
        'name' => 'min:4',
        'email' => 'email|unique:users',
        'password' => 'min:8',
        'activated_at' => 'date'
    ];

    public $loginRules = [
        'email' => 'required|email|exists:users',
        'password' => 'required'
    ];

    public function locations()
    {
        return $this->hasMany('App\Location');
    }

    public function accessToken()
    {
        return $this->hasOne('App\OauthAccessToken');
    }

    public function updateFields($fieldsData)
    {
        $fieldsArray = [];
        foreach ($fieldsData as $fieldKey => $fieldData) {
            if (in_array($fieldKey, $this->fillable)) {
                $fieldsArray[$fieldKey] = $fieldData;
            }
        }

        if (!$this->validate($fieldsArray, $this->updateRules)) {
            return false;
        }

        if ($this->update($fieldsArray)) {
            return true;
        }

        return false;
    }

    public function tutor()
    {
        return $this->hasOne('App\Tutor');
    }

    public function student()
    {
        return $this->hasOne('App\Student');
    }

    /**
     * Return the relevant user type: tutor or student
     * 
     * TODO: Maybe change this to polymorphic relation
     */
    public function type()
    {   
        if (!is_null($this->student))
            return 'student';

        if (!is_null($this->tutor))
            return 'tutor';
    }
}
