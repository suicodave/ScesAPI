<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role(){
        return $this->hasOne("App\Role","id","role_id");
    }
    public function isSuperUser(){
        return $this->role()->where("name","Super User")->exists();
    }

    public function isStudent(){
        return $this->role()->where("name","Student")->exists();
    }

    public function isComelec(){
        return $this->role()->where("name","Comelec Officer")->exists();
    }

    public function isRegistrar(){
        return $this->role()->where("name","Registrar Officer")->exists();
    }
}
