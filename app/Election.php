<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    public function school_year(){
        return $this->hasOne('App\SchoolYear','id','school_year_id');
    }

    public function processed_by(){
        return $this->hasOne('App\User','id','processor_id');
    }
}
