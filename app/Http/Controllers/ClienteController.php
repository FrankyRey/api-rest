<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Cliente;

class ClienteController extends Controller
{
    public function __construct() {
		$this->middleware('api.auth', ['except' => [
			'index',
            'show',
            'store',
            'email',
		]]);
	}

	public function index() {
        $clientes = Cliente::all();

		if(!empty($clientes)) {
			$data = array(
				'code'		=> 200,
				'status'	=> 'success',
				'clientes'	=> $clientes	
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
    
    public function email($email, Request $request) {
        $cliente = Cliente::where('email', $email)->first();

        if(!empty($cliente)) {
            $data = array(
                'code'      => 200,
                'status'    => 'success',
                'cliente'   => $cliente
            );
        } else {
            $data = array(
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'Cliente no encontrado',
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
    			'email'	=> 'required',
                'name'  => 'required',
                'last_name' => 'required',
                'birthdate' => 'required',
    		]);

    		if($validate->fails()) {
    			$data = [
    				'code'		=> 400,
    				'status'	=> 'error',
    				'message'	=> 'Faltan datos',
    			];
    		} else {
    			// Guardar la categoria
    			$cliente = new Cliente();
    			
    			$cliente->email = $params->email;
                $cliente->name = $params->name;
                $cliente->last_name = $params->last_name;
                $cliente->birthdate = $params->birthdate;
                $cliente->phone_number = $params->phone_number;

   				$cliente->save();
    			
    			$data = [
    				'code'		=> 200,
    				'status'	=> 'success',
    				'cliente'	=> $cliente,
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

    			$estatusCliente = EstatusCliente::where('id', $id)->update($params_array);

    			$estatusCliente_o = EstatusCliente::find($id);

    			$data = array(
    				'code'		=> 200,
    				'status'	=> 'success',
    				'estatusCliente'		=>	$estatusCliente_o,
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
