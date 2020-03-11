<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\CategoriaProducto;

class CategoriaProductoController extends Controller
{
    public function __construct() {
		$this->middleware('api.auth', ['except' => [
			'index',
			'show',
			'publicados',
		]]);
	}

	public function index() {
		$categoriasProductos = CategoriaProducto::all();

		if(!empty($categoriasProductos)) {
			$data = array(
				'code'		=> 200,
				'status'	=> 'success',
				'categoriasProductos'	=> $categoriasProductos	
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
    			'descripcion'	=> 'required',
    		]);

    		if($validate->fails()) {
    			$data = [
    				'code'		=> 400,
    				'status'	=> 'error',
    				'message'	=> 'Faltan datos',
    			];
    		} else {
    			// Guardar la categoria
    			$categoriaProducto = new CategoriaProducto();
    			
    			$categoriaProducto->nombre = $params->descripcion;

   				$categoriaProducto->save();
    			
    			$data = [
    				'code'		=> 200,
    				'status'	=> 'success',
    				'categoriaProducto'	=> $categoriaProducto,
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
}
