<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    protected $table = 'ordenes';

    //Relación muchos a uno
    public function estatusO(){
    	return $this->belongsTo('App\EstatusOrden', 'estatus');
    }

    public function create(){
    	return $this->belongsTo('App\User', 'create_by');
    }

    public function cobrada(){
        return $this->belongsTo('App\User', 'cobrada_por');
    }
}
