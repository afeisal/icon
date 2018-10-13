<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
class Code extends Model
{
    use HasRoles;
    protected $fillable=['client_name','proposal_value','proposal_date','code','technical_id','source_id','sale_agent','type_id'];

    public function type(){
       return $this->belongsTo('App\Type','type_id');
    }
    public function source(){
       return $this->belongsTo('App\Source','source_id');
    }
    public function Technical(){
       return $this->belongsTo('App\Technical','technical_id');
    }
    public function agent(){
        return $this->belongsTo('App\User','sale_agent');
    }
}
