<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    
    public function yearLevels(){
        return $this->hasMany('App\YearLevel');
    }

    public function students(){
        return $this->hasMany('App\Student','department_id','id');
    }

}
