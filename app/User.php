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

    /**
     * The validation rules for creating a new user
     *
     * @var array
     */
    public $creationRules = [
        'name' => 'required|min:4',
        'email' => 'required|email|unique:users',
        'password' => 'min:8'
    ];

    /**
     * The validation rules for updating a user
     *
     * @var array
     */
    public $updateRules = [
        'name' => 'min:4',
        'email' => 'email|unique:users',
        'password' => 'min:8',
        'activated_at' => 'date'
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
}
