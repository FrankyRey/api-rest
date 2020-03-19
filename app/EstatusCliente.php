<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstatusCliente extends Model
{
    protected $table = 'estatus_clientes';

    //Relación uno a muchos
    public function clientes() {
    	return $this->hasMany('App\Cliente', 'estatus');
    }
}
