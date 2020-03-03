<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Orden;
use App\Helpers\JwtAuth;

class OrdenController extends Controller
{
    public function __construct() {
		$this->middleware('api.auth', ['except' => ['index','show']]);
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
    		$jwtAuth = new JwtAuth();
    		$token = $request->header('Authorization', null);
    		$user = $jwtAuth->checkToken($token, true);

    		//  Validar los datos
    		$validate = \Validator::make($params_array, [
    			'monto'		=> 'required',
    			'estatus'	=> 'required',
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
    				$orden->create_by  = $user->sub;
    				$orden->estatus = $params->estatus;
    				$orden->monto = $params->monto;
    				$orden->facturable = 1;
    				$orden->save();
    			} else {
    				$orden->create_by  = $user->sub;
    				$orden->estatus = $params->estatus;
    				$orden->monto = $params->monto;
    				$orden->facturable = 0;
    				$orden->save();
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
}
