<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class YearLevel extends Model
{   
    use SoftDeletes;

    public function department(){
        return $this->belongsTo('App\Department','department_id');
    }

    protected $fillable = ['name','department_id'];
}
