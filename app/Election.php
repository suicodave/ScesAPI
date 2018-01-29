<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    public function school_year()
    {
        return $this->hasOne('App\SchoolYear', 'id', 'school_year_id');
    }

    public function processed_by()
    {
        return $this->hasOne('App\User', 'id', 'processor_id');
    }

    public function positions()
    {
        return $this->hasMany('App\Position', 'election_id', 'id');
    }

    public function partylists()
    {
        return $this->hasMany('App\Partylist', 'election_id', 'id');
    }

    public function candidates(){
        return $this->hasMany('App\Candidate','election_id','id');
    }
}
