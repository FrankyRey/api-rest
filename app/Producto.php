<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    //Relación muchos a uno
    public function estatus() {
    	return $this->belongsTo('App\EstatusProducto', 'estatus');
    }

    public function categoria() {
    	return $this->belongsTo('App\CategoriaProducto', 'categoria');
    }
}
