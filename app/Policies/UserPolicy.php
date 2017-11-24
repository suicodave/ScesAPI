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

    // User Profiles

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


    // School Year

    public function storeSchoolYear(){ //superuser only
        return $this->isSuperUser();
    }

    public function updateSchoolYear(){ //superuser only
        return $this->isSuperUser();
    }

    public function deleteSchoolYear(){ //superuser only
        return $this->isSuperUser();
    }

    public function restoreSchoolYear(){ //superuser only
        return $this->isSuperUser();
    }

    public function activateSchoolYear(){ //superuser only
        return $this->isSuperUser();
    }


    // Year Level

    public function storeYearLevel(){ //superuser only
        return $this->isSuperUser();
    } 

    public function updateYearLevel(){ //superuser only
        return $this->isSuperUser();
    } 
    public function deleteYearLevel(){ //superuser only
        return $this->isSuperUser();
    }
    public function restoreYearLevel(){ //superuser only
        return $this->isSuperUser();
    }


    // College

    public function storeCollege(){ //superuser only
        return $this->isSuperUser();
    }
    
    public function updateCollege(){ //superuser only
        return $this->isSuperUser();
    }  
    
    public function deleteCollege(){ //superuser only
        return $this->isSuperUser();
    }
    public function restoreCollege(){ //superuser only
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
