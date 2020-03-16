<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Producto;

class ProductoController extends Controller
{
    public function __construct() {
		$this->middleware('api.auth', ['except' => ['index',
			'show',
			'store',
		]]);
	}

    public function index() {
    	$productos = Producto::all()->load('categoria', 'estatus');

    	return response()->json([
    		'code'		=> 200,
    		'status'	=> 'success',
    		'productos'	=> $productos
    	], 200);
    }

    public function store(Request $request) {
		// Recoger datos por POST
    	$json = $request->input('json', null);
    	$params = json_decode($json);
    	$params_array = json_decode($json, true);;

    	if(!empty($params_array)) {
    		//  Validar los datos

    		$validate = \Validator::make($params_array, [
    			'descripcion_corta'	=> 'required',
    			'inventario'	=> 'required',
    			'costo'	=> 'required',
    			'precio_venta'	=> 'required',
    			'estatus'	=> 'required',
    			'categoria'	=> 'required',
    		]);

    		if($validate->fails()) {
    			$data = [
    				'code'		=> 400,
    				'status'	=> 'error',
    				'message'	=> 'Faltan datos',
    			];
    		} else {
    			// Guardar el producto
    			$producto = new Producto();
    			
    			$producto->descripcion_corta = $params->descripcion_corta;
    			$producto->inventario = $params->inventario;
    			$producto->costo = $params->costo;
    			$producto->precio_venta = $params->precio_venta;
    			$producto->estatus = $params->estatus;
    			$producto->categoria = $params->categoria;

   				$producto->save();
    			
    			$data = [
    				'code'		=> 200,
    				'status'	=> 'success',
    				'producto'	=> $producto,
    			];
    		}
    	} else {
    		$data = [
    			'code'		=> 400,
    			'status'	=> 'error',
   				'message'	=> 'Faltan datos',
    		];
    	}

    	return response()->json($data, $data['code']);
	}

	public function update($id, Request $request) {
    	// Recoger los datos por POST
    	$json = $request->input('json', null);
    	$params_array = json_decode($json, true);

    	if(!empty($params_array)) {
    		// Validar datos
    		$validate = \Validator::make($params_array, [
    			'descripcion_corta'	=> 'required',
    			'inventario'	=> 'required',
    			'costo'	=> 'required',
    			'precio_venta'	=> 'required',
    			'estatus'	=> 'required',
    			'categoria'	=> 'required',
    		]);

    		if($validate->fails()) {
    			$data = array(
    				'code'		=> 400,
    				'status'	=> 'error',
    				'message'	=> 'Faltan Datos',
    			);
    		} else {
    			//Eliminar lo que no queremos actualizar
    			unset($params_array['id']);
    			unset($params_array['created_at']);

    			$producto = Producto::where('id', $id)->update($params_array);

    			$producto_o = Producto::find($id);

    			$data = array(
    				'code'		=> 200,
    				'status'	=> 'success',
    				'producto'		=>	$producto_o,
    				'changes'	=> $params_array,
    			);
    		}
    	} else {
    		$data = array(
    			'code'		=> 400,
    			'status'	=> 'error',
    			'message'	=> 'Datos erroneos',
    		);
    	}

    	return response()->json($data, $data['code']);
    }
}
