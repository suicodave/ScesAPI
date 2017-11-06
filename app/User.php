<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey(); // Eloquent Model method
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

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

    public function adminProfile(){
        return $this->hasOne("App\Admin","user_id","id");
    }


}
