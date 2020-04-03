<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    public function estatus() {
    	return $this->belongsTo('App\EstatusCliente', 'estatus');
    }

    //Relación uno a muchos
    public function clientes(){
        return $this->hasMany('App\Cliente');
    }
}
