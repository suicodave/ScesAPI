<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    public function candidates()
    {
        return $this->hasMany('App\Candidate', 'position_id', 'id');
    }

    public function election()
    {
        return $this->hasOne('App\Election', 'id', 'election_id');
    }
    public function college()
    {
        return $this->hasOne('App\College', 'id', 'col_id');
    }
}
