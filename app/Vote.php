<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    public function candidate()
    {
        return $this->hasOne('App\Candidate', 'id', 'candidate_id');
    }
}
