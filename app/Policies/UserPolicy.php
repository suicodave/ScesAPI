<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use JWTAuth;
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function viewAdmin()
    {
        return $this->isRegistrar() || $this->isSuperUser();
    }

    public function storeRegistrar(){
        
        return $this->isRegistrar() || $this->isSuperUser();
    }

    public function updateRegistrar(){
        
        return $this->isRegistrar();
        
    }


    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        //
    }



    public function isSuperUser(){
        $token = JWTAuth::toUser();
        $user = User::with("role")->find($token)->first();
        return ( $user->role->name == "Super User" );
    }

    public function isStudent(){
        $token = JWTAuth::toUser();
        $user = User::with("role")->find($token)->first();
        return ( $user->role->name == "Student" );
    }

    public function isComelec(){
        $token = JWTAuth::toUser();
        $user = User::with("role")->find($token)->first();
        return ( $user->role->name == "Comelec Officer" );
    }

    public function isRegistrar(){
        $token = JWTAuth::toUser();
        $user = User::with("role")->find($token)->first();
        return ( $user->role->name == "Registrar Officer" );
    }
}
