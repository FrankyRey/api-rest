<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstatusProducto extends Model
{
    protected $table = 'estatus_productos';

    //Relación uno a muchos
    public function productos() {
    	return $this->hasMany('App\Producto', 'estatus');
    }
}
