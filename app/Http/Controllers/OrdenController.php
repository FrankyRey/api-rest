<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Orden;
use App\Cliente;
use App\Helpers\JwtAuth;

class OrdenController extends Controller
{
    public function __construct() {
		$this->middleware('api.auth', ['except' => ['index',
			'show', 
			'getOrdenByEstatus',
			'getOdenByUsuario',
		]]);
	}

    public function index() {
    	$ordenes = Orden::all()->load('estatusO', 'create');

    	return response()->json([
    		'code'		=> 200,
    		'status'	=> 'success',
    		'ordenes'	=> $ordenes
    	], 200);
    }

    public function show($id) {
    	$orden = Orden::find($id);

    	if(is_object($orden)) {
    		$orden = Orden::find($id)->load('estatusO', 'create');

    		$data = [
    			'code'		=> 200,
    			'status'	=> 'succes',
    			'orden'		=>	$orden,
    		];
    	} else {
    		$data = [
    			'code'		=> 400,
    			'status'	=> 'error',
    			'message'	=>	'No existe la orden',
    		];
    	}

    	return response()->json($data, $data['code']);
    }

    public function store(Request $request) {
    	// Recoger datos por POST
    	$json = $request->input('json', null);
    	$params = json_decode($json);
    	$params_array = json_decode($json, true);

    	if(!empty($params_array)) {
    		// Conseguir el usuario identificado
    		$user = $this->getIdentity($request);

    		//  Validar los datos
    		$validate = \Validator::make($params_array, [
    			'monto'		=> 'required',
    		]);

    		if($validate->fails()) {
    			$data = [
    				'code'		=> 400,
    				'status'	=> 'error',
    				'message'	=> 'Faltan datos',
    			];
    		} else {
    			// Guardar la orden
    			$orden = new Orden();
    			if($params->monto>=10) {
					if($params->procedencia == 1) {
    					$orden->cliente = $params->cliente;
    					$orden->estatus = $params->estatus;
    					$orden->monto = $params->monto;
    					$orden->facturable = 1;
						$orden->save();
					}
    			} else {
    				if($params->procedencia == 1) {
    					$orden->cliente = $params->cliente;
    					$orden->estatus = $params->estatus;
    					$orden->monto = $params->monto;
    					$orden->facturable = 1;
						$orden->save();
					}
    			}

    			$data = [
    				'code'		=> 200,
    				'status'	=> 'success',
    				'orden'		=> $orden,
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
    			'monto'		=> 'required',
    			'estatus'	=> 'required'
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

    			$orden = Orden::where('id', $id)->update($params_array);

    			$orden_o = Orden::find($id);

    			$data = array(
    				'code'		=> 200,
    				'status'	=> 'success',
    				'orden'		=>	$orden_o,
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

    public function destroy($id, Request $request) {
    	// Conseguir el registro
    	$orden = Orden::find($id);

    	if(!empty($orden)) {
    		// Borarlo
    		$orden->delete();

    		// Devolver algo
    		$data = array(
    			'code'		=> 200,
    			'status'	=> 'success',
    			'orden'		=> $orden
    		);
    	} else {
    		$data = array(
    			'code'		=> 400,
    			'status'	=> 'error',
    			'message'	=> 'La orden no existe',
    		);
    	}

    	return response()->json($data, $data['code']);
    }

    private function getIdentity($request){
    	// Conseguir el usuario identificado
    	$jwtAuth = new JwtAuth();
    	$token = $request->header('Authorization', null);
   		$user = $jwtAuth->checkToken($token, true);

   		return $user;
    }

    public function getOrdenByEstatus($id) {
    	$ordenes = Orden::where('estatus', $id)->get();

    	return response()->json([
    		'status'	=> 'success',
    		'ordenes'	=> $ordenes,
    	], 200);
    }

    public function getOrdenByUsuario($id) {
    	$ordenes = Orden::where('create_by', $id)->get();

    	return response()->json([
    		'status'	=> 'success',
    		'ordenes'	=> $ordenes,
    	], 200);
    }
}
