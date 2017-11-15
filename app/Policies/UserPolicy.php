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
    public function viewAdmin() //registrar or superuser
    {
        return $this->isRegistrar() || $this->isSuperUser();
    }

    public function storeRegistrar(){ // registrar or superuser
        
        return $this->isRegistrar() || $this->isSuperUser();
    }

    public function updateRegistrar(){ //registrar only
        
        return $this->isRegistrar();
        
    }

    public function storeSchoolYear(){ //superuser only
        return $this->isSuperUser();
    }


   
    
    ##################################################


    private function isSuperUser(){
        $user = JWTAuth::toUser();
        return $user->isSuperUser();
        
        
    }

    private function isStudent(){
        $user = JWTAuth::toUser();
        return $user->isStudent();
        
    }

    private function isComelec(){
        $user = JWTAuth::toUser();
        return $user->isComelec();
        
    }

    private function isRegistrar(){
        $user = JWTAuth::toUser();
        return $user->isRegistrar();
        
    }
}
