<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Boleto;

class BoletoController extends Controller
{
    public function __construct() {
		$this->middleware('api.auth', ['except' => [
			'index',
			'show',
			'publicados',
		]]);
	}

	public function index() {
		$boletos = Boleto::all();

		$data = array(
			'code'		=> 200,
			'status'	=> 'success',
			'boletos'	=> $boletos	
		);

		return response()->json($data, $data['code']);
	}

	public function publicados() {
		$boletos = Boleto::where('publicado', 1)->get();

		$data = array(
			'code'		=> 200,
			'status'	=> 'success',
			'boletos'	=> $boletos	
		);

		return response()->json($data, $data['code']);
	}

	public function show($id) {
		$boleto = Boleto::find($id);

		if(!empty($boleto)) {
			$data = array(
				'code'		=> 200,
				'status'	=> 'success',
				'boleto'	=> $boleto,
			);
		} else {
			$data = array(
				'code'		=> 400,
				'status'	=> 'error',
				'message'	=> 'Este boleto no existe',
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
    			'precio_venta'	=> 'required',
    		]);

    		if($validate->fails()) {
    			$data = [
    				'code'		=> 400,
    				'status'	=> 'error',
    				'message'	=> 'Faltan datos',
    			];
    		} else {
    			// Guardar el boleto
    			$boleto = new Boleto();
    			
    			$boleto->descripcion = $params->descripcion;
    			$boleto->precio_venta = $params->precio_venta;

   				$boleto->save();
    			
    			$data = [
    				'code'		=> 200,
    				'status'	=> 'success',
    				'boleto'	=> $boleto,
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

	public function destroy($id, Request $request) {
    	// Conseguir el registro
    	$boleto = Boleto::find($id);

    	if(!empty($boleto)) {
    		// Borarlo
    		$boleto->delete();

    		// Devolver algo
    		$data = array(
    			'code'		=> 200,
    			'status'	=> 'success',
    			'boleto'		=> $boleto
    		);
    	} else {
    		$data = array(
    			'code'		=> 400,
    			'status'	=> 'error',
    			'message'	=> 'El boleto no existe',
    		);
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
    			'descripcion'	=> 'required',
    			'precio_venta'	=> 'required'
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
    			unset($params_array['action']);
    			unset($params_array['button']);

    			$boleto = Boleto::where('id', $id)->update($params_array);

    			$boleto_o = Boleto::find($id);

    			$data = array(
    				'code'		=> 200,
    				'status'	=> 'success',
    				'boleto'		=>	$boleto_o,
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
