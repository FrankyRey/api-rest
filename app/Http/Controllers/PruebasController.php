<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Orden;
use App\EstatusOrden;
use App\User;

class PruebasController extends Controller
{
    public function index(){
        $titulo = 'Animales';
        $animales = ['Perro', 'Gato', 'Tigre'];

        return view('pruebas.index', array(
            'titulo' => $titulo,
            'animales' => $animales
        ));
    }

    public function testOrm(){
		/*
    	$ordenes = Orden::all();
    	foreach ($ordenes as $orden) {
    		echo "<h1>".$orden->monto."</h1>";
    		echo "<p>Estatus: {$orden->estatusO->nombre}</p>";
    		echo "<p> Creada por: {$orden->create->name} {$orden->create->last_name} </p>";
    	}
    	*/
    	$estatusOrdenes = EstatusOrden::all();
    	foreach ($estatusOrdenes as $estatusOrden) {
    		echo "<h1>".$estatusOrden->nombre."</h1>";
    		echo "<h2>".$estatusOrden->id."</h2>";
    		foreach ($estatusOrden->ordenes as $orden) {
    			echo "<h1>".$orden->monto."</h1>";
    			echo "<p>Estatus: {$orden->estatusO->nombre}</p>";
    			echo "<p> Creada por: {$orden->create->name} {$orden->create->last_name} </p>";
    		}
    	}
    	

    	die ();
    }
}
