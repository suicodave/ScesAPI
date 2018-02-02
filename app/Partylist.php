<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partylist extends Model
{
    public function candidates()
    {
        return $this->hasMany('App\Candidate', 'partylist_id', 'id');
    }
}
