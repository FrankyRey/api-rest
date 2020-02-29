<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstatusOrden extends Model
{
    protected $table = 'estatus_ordenes';

    //Relación uno a muchos
    public function ordenes(){
    	return $this->hasMany('App\Orden', 'estatus');
    }
}
