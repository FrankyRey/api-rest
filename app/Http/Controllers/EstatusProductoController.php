<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\EstatusProducto;

class EstatusProductoController extends Controller
{
    public function __construct() {
		$this->middleware('api.auth', ['except' => [
			'index',
			'show',
		]]);
	}

	public function index() {
		$estatusProductos = EstatusProducto::all();

		if(!empty($estatusProductos)) {
			$data = array(
				'code'		=> 200,
				'status'	=> 'success',
				'estatusProductos'	=> $estatusProductos	
			);
		} else {
			$data = array(
				'code'		=> 200,
				'status'	=> 'error',
				'message'	=> 'Sin datos encontrados',
			);
		}

		return response()->json($data, $data['code']);
	}

	public function store(Request $request) {
		// Recoger datos por POST
    	$json = $request->input('json', null);
    	$params = json_decode($json);
    	$params_array = json_decode($json, true);

    	if(!empty($params_array)) {
    		//  Validar los datos
    		$validate = \Validator::make($params_array, [
    			'nombre'	=> 'required',
    		]);

    		if($validate->fails()) {
    			$data = [
    				'code'		=> 400,
    				'status'	=> 'error',
    				'message'	=> 'Faltan datos',
    			];
    		} else {
    			// Guardar la categoria
    			$estatusProducto = new EstatusProducto();
    			
    			$estatusProducto->nombre = $params->nombre;

   				$estatusProducto->save();
    			
    			$data = [
    				'code'		=> 200,
    				'status'	=> 'success',
    				'estatusProducto'	=> $estatusProducto,
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
    			'nombre'	=> 'required',
    		]);

    		if($validate->fails()) {
    			$data = array(
    				'code'		=> 400,
    				'status'	=> 'error',
    				'message'	=> 'Datos erroneos',
    			);
    		} else {
    			//Eliminar lo que no queremos actualizar
    			unset($params_array['id']);
    			unset($params_array['created_at']);
    			unset($params_array['create_by']);

    			$estatusProducto = EstatusProducto::where('id', $id)->update($params_array);

    			$estatusProducto_o = EstatusProducto::find($id);

    			$data = array(
    				'code'		=> 200,
    				'status'	=> 'success',
    				'estatusProducto'		=>	$estatusProducto_o,
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
