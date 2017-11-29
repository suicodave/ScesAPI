<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Registrar extends Model
{
    use SoftDeletes;


    public function processor(){
        return $this->hasOne('App\User','id','processed_by');
    }

    public function user(){
        return $this->belongsTo('App\User','user_id','id');
    }
}
