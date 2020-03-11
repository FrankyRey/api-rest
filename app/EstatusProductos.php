<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstatusProductos extends Model
{
    protected $table = 'estatus_productos';

    //Relación uno a muchos
    public function productos() {
    	return $this->hasMany('App\Producto', 'estatus_producto');
    }
}
