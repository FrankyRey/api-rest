<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\EstatusOrden;

class EstatusOrdenController extends Controller
{

	public function __construct() {
		$this->middleware('api.auth', ['except' => ['index','show']]);
	}

    public function index() {
    	$estatusOrden = EstatusOrden::all();

    	return response()->json([
    		'code'		=> 200,
    		'status'	=> 'success',
    		'estatusOrden'	=> $estatusOrden
    	]);
    }

    public function show($id) {
    	$estatusOrden = EstatusOrden::find($id);

    	if(is_object($estatusOrden)) {
    		$data = [
    			'code'		=> 200,
    			'status'	=> 'success',
    			'estatusOrden'	=> $estatusOrden,
    		];
    	} else {
    		$data = [
    			'code'		=> 404,
    			'status'	=> 'error',
    			'message'	=> 'El estatus de la orden no existe',
    		];
    	}
    	return response()->json($data, $data['code']);
    }

    public function store(Request $request) {
    	// Recoger los datos por POST
    	$json = $request->input('json', null);
    	$params_array = json_decode($json, true);

    	if(!empty($params_array)) {
    		// Validar los datos
    		$validate = \Validator::make($params_array, [
    			'nombre'	=> 'required',
    		]);

    		if($validate->fails()){
    			$data = [
    				'code'		=> 400,
    				'status'	=> 'error',
    				'message'	=> 'No se ha guardado la categoria',
    			];
    		} else {
    			$estatusOrden = new EstatusOrden();
    			$estatusOrden->nombre = $params_array['nombre'];
    			$estatusOrden->save();

    			$data = [
    				'code'		=> 200,
    				'status'	=> 'success',
    				'estatusOrden'	=> $estatusOrden,
    			];
    		}
    	} else {
    		$data = [
    			'code'		=> 400,
    			'status'	=> 'error',
    			'message'	=> 'No existen datos',
   			]; 
    	}

    	return response()->json($data, $data['code']);
    }

    public function update($id, Request $request) {
    	// Recoger los datos por POST
    	$json = $request->input('json', null);
    	$params_array = json_decode($json, true);

    	if(!empty($params_array)) {
    		// Validar los datos
    		$validate = \Validator::make($params_array, [
    			'nombre'	=> 'required',
    		]);

    		// Quitar lo que no quiero actualizar
    		unset($params_array['id']);
    		unset($params_array['created_at']);

    		// Actualizar el registro
    		$estatusOrden = EstatusOrden::where('id', $id)->update($params_array);

    		if($estatusOrden) {
    			// Devolver los datos
    			$data = [
    				'code'		=> 200,
    				'status'	=> 'success',
    				'estatusOrden'	=> $params_array,
   				];
   			} else {
   				$data = [
    				'code'		=> 400,
    				'status'	=> 'error',
    				'message'	=> 'No se pudo actualizar, estatus inexistente',
   				];
   			}

    	} else {
    		$data = [
    			'code'		=> 400,
    			'status'	=> 'error',
    			'message'	=> 'No existen datos',
   			]; 
    	}

    	return response()->json($data, $data['code']);
    }
}
