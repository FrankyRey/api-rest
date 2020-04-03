<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
Use Carbon\Carbon;

use App\DesgloseOrden;

class DesgloseOrdenController extends Controller
{
    public function __construct() {
		$this->middleware('api.auth', ['except' => [
			'index',
            'show',
            'store',
		]]);
	}

	public function index() {
		$desgloseOrdenes = DesgloseOrden::all();

		$data = array(
			'code'		=> 200,
			'status'	=> 'success',
			'desgloseOrdenes'	=> $desgloseOrdenes	
		);

		return response()->json($data, $data['code']);
    }
    
    public function store(Request $request) {
        $flag = false;
		// Recoger datos por POST
    	$json = $request->input('json', null);
        $params = json_decode($json);
        
        foreach($params->carrito as $objeto) {
            $params_array = json_decode(json_encode($objeto), true);
            
            if(!empty($params_array)) {
                //  Validar los datos
    		    $validate = \Validator::make($params_array, [
    			    'cantidad'	        => 'required',
                    'precio_venta'	=> 'required',
    		    ]);

    		    if($validate->fails()) {
                    $flag = false;
    			    $data = [
    				    'code'		=> 400,
    				    'status'	=> 'error',
    				    'message'	=> 'Faltan datos',
                    ];
                    break;
    		    } else {
                    if( $params->procedencia == 1) {
                        $flag = true;

                        // Armo guardado
                        $insert[] = [
                            'cantidad'      => $objeto->cantidad,
                            'costo_unitario'    => $objeto->precio_venta,
                            'costo_total'       => $objeto->cantidad * $objeto->precio_venta,
                            'boleto'            => $objeto->id,
                            'orden'             => $params->orden,
                            'created_at'        => Carbon::now('America/Mexico_City'),
                            'updated_at'        => Carbon::now('America/Mexico_City'),
                        ];
                    }
    		    }
    	    } else {
                $flag = false;
    		    $data = [
    			    'code'		=> 400,
    			    'status'	=> 'error',
   				    'message'	=> 'Error',
                ];
                break;
            }
        }

        if($flag){
            DesgloseOrden::insert($insert);
            $data = [
                'code'      => 200,
                'status'    => 'success',
                'desgloseOrden' => $insert,
            ];
        }

    	return response()->json($data, $data['code']);
	}
}
