<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Technical extends Model
{
    protected $table='technicals';
    public function codes(){
        return $this->hasMany('App\Code','technical_id');
    }
}
