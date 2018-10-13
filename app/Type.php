<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $table='types';
    public function codes(){
        return $this->hasMany('App\Code','type_id');
    }
}
