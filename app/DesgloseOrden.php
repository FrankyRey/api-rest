<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DesgloseOrden extends Model
{
    protected $table = 'desglose_ordenes';

    public function boletos() {
    	return $this->belongsTo('App\Boleto', 'boleto');
    }

    public function productos() {
    	return $this->belongsTo('App\Producto', 'producto');
    }

    public function ordenes() {
    	return $this->belongsTo('App\Orden', 'orden');
    }
}
