<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model
{
    protected $table = 'categorias_productos';

    //Relación uno a muchos
    public function ordenes() {
    	return $this->hasMany('App\Producto', 'categoria');
    }
}
