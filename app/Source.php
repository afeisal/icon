<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $table='sources';
    public function codes(){
        return $this->hasMany('App\Code','source_id');
    }
}
