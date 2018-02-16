<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    public function student()
    {
        return $this->hasOne('App\Student', 'id', 'student_id');
    }
    public function election()
    {
        return $this->hasOne('App\Election', 'id', 'election_id');
    }
    public function position()
    {
        return $this->hasOne('App\Position', 'id', 'position_id');
    }
    public function partylist()
    {
        return $this->hasOne('App\Partylist', 'id', 'partylist_id');
    }
    public function votes()
    {
        return $this->hasMany('App\Vote', 'candidate_id', 'id');
    }
}
