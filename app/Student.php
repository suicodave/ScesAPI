<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Student extends Model
{
    use Searchable;
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo('App\Department', 'department_id', 'id');
    }
    public function year_level()
    {
        return $this->belongsTo('App\YearLevel', 'year_level_id', 'id');
    }

    public function college()
    {
        return $this->belongsTo('App\College', 'college_id', 'id');
    }

    public function school_year()
    {
        return $this->belongsTo('App\SchoolYear', 'school_year_id', 'id');
    }

    public function processor(){
        return $this->hasOne('App\User','id','processed_by');
    }

    
}
