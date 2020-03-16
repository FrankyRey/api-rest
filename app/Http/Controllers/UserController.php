<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function pruebas(Request $request) {
    	return "Accion de pruebas de USER-CONTROLLER";
    }

    public function index() {
        $usuarios = User::all();

        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'usuarios' => $usuarios
        ], 200);
    }

    public function register(Request $request) {

    	//Recoger los datos del usuario por POST
    	$json = $request->input('json', null);
    	$params = json_decode($json);
    	$params_array = json_decode($json, true);

    	if(!empty($params) && !empty($params_array)){

    		//Limpiar datos con trim
    		$params_array = array_map('trim',$params_array);
    	
	    	//Validar datos, unique para campos unicos
    		$validate = \Validator::make($params_array, [
    			'name' 		=> 'required|alpha',
    			'last_name'	=> 'required|alpha',
    			'username'	=> 'required|alpha|unique:users',
	    		'email'		=> 'required|email|unique:users',
    			'password'	=> 'required'
    		]);

    		if($validate->fails()) {

    			//Validación ha fallado
    			$data = array(
    				'status'  => 'error',
    				'code'    =>  404,
    				'message' => 'El usuario no se ha creado',
	    			'errors'	=> $validate->errors()
    			);

    			return response()->json($data, $data['code']);
    		} else {

    			//Validación pasada correctamente

    			//Cifrar contraseña, el costo son las veces que se va a cifrar la contraseña
    			$pwd = hash('sha256', $params->password);

    			//Crear el usuario
    			$user = new User();
    			$user->name 	= $params_array['name'];
    			$user->last_name	= $params_array['last_name'];
    			$user->username = $params_array['username'];
    			$user->email 	= $params_array['email'];
    			$user->password = $pwd;
    			$user->role 	= 'ROLE_USER';

    			//Guardar el usuario
    			$user->save();

    			$data = array(
    				'status'	=> 'success',
    				'code'		=> 200,
    				'message'	=> 'El usuario se ha creado correctamente',
    				'user'		=> $user,
    			);

    			return response()->json($data, $data['code']);
    		}
    	} else {
    		$data = array(
    			'status'	=> 'error',
    			'code'		=> 404,
   				'message'	=> 'Los datos enviados no son correctos'
   			);
    	}

    	return response()->json($data, $data['code']);
    }

    public function login(Request $request) {

    	$jwtAuth = new \JwtAuth();

    	// Recibir datos por POST
    	$json = $request->input('json', null);
    	$params = json_decode($json);
    	$params_array = json_decode($json, true);

    	// Validar esos datos
    	$validate = \Validator::make($params_array, [
    			'username'	=> 'required|alpha',
    			'password'	=> 'required'
    		]);

    		if($validate->fails()) {

    			//Validación ha fallado
    			$singup = array(
    				'status'  => 'error',
    				'code'    =>  404,
    				'message' => 'El usuario no se ha creado podido identificar',
	    			'errors'	=> $validate->errors()
    			);

    			return response()->json($singup, $singup['code']);
    		} else {

    			// Cifrar la contraseña
    			$pwd = hash('sha256', $params->password);

    			// Devolver token o datos
    			$singup = $jwtAuth->singup($params->username, $pwd);

    			if(!empty($params->getToken)) {
    				$singup = $jwtAuth->singup($params->username, $pwd, true);
    			}
    		}

    	return response()->json($singup,200);
    }

    public function update(Request $request) {

    	// Comprobar si el usuario esta identificado
    	$token = $request->header('Authorization');

    	$jwtAuth = new \JwtAuth();
    	$checkToken = $jwtAuth->checkToken($token);

    	// Recoger los datos por POST
    	$json = $request->input('json', null);
    	$params_array = json_decode($json, true);

    	if($checkToken && !empty($params_array)) {
    		
    		// Actualizar el usuario

    		// Sacar usuario identificado
    		$user = $jwtAuth->checkToken($token, true);

    		// Validar datos
    		$validate = \Validator::make($params_array, [
    			'name' 		=> 'required|alpha',
    			'last_name'	=> 'required|alpha',
	    		'email'		=> 'required|email|unique:users,'.$user->sub,
    		]);

    		// Quitar los campos que no quiero actualizar
    		unset($params_array['id']);
    		unset($params_array['role']);
    		unset($params_array['password']);
    		unset($params_array['created_at']);
    		unset($params_array['remember_token']);
    		unset($params_array['username']);

    		// Actualizar usuario en bdd
    		$user_update = User::where('id', $user->sub)->update($params_array);

    		// Devolver array con resultado
    		$data = array(
    			'code' 		=> 200,
    			'status'	=> 'success',
    			'usuario'	=> $user,
    			'changes'	=> $params_array,
    		);

    	} else {
    		$data = array(
    			'code' 		=> 400,
    			'status'	=> 'error',
    			'message'	=> 'El usuario no esta identificado',
    		);
    	}

    	return response()->json($data, $data['code']);
    }

    public function upload(Request $request) {

    	// Recoger datos de la petición
    	$image = $request->file('file0');

    	// Validación de imagen
    	$validate = \Validator::make($request->all(), [
    		'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
    	]);

    	// Guardar imagen
    	if(!$image || $validate->fails()) {
    		$data = array(
    			'code' 		=> 400,
    			'status'	=> 'error',
   				'message'	=> 'Error al subir la imagen',
   			);
    	} else {
    		$image_name = time().$image->getClientOriginalName();
    		\Storage::disk('users')->put($image_name, \File::get($image));

    		$data = array(
    			'image'		=> $image_name,
    			'code'		=> 200,
    			'status'	=> 'success'
    		);
    	}
   		return response($data, $data['code'])->header('Content-Type', 'text/plain');
    }

    public function getImage($filename) {
    	$isset = \Storage::disk('users')->exists($filename);

    	if($isset) {
    		$file = \Storage::disk('users')->get($filename);

    		return new Response($file, 200);
    	} else {
    		$data = array(
    			'code' 		=> 404,
    			'status'	=> 'error',
   				'message'	=> 'La imagen no existe',
   			);

   			return response()->json($data, $data['code']);
    	}
    }

    public function profile($id) {
    	$user = User::find($id);

    	if(is_object($user)) {
    		$data = array(
    			'code'		=> 200,
    			'status'	=> 'success',
    			'user'		=> $user
    		);
    	} else {
    		$data = array(
    			'code'		=> 404,
    			'status'	=> 'error',
    			'message'		=> 'El usuario no existe'
    		);
    	}

    	return response()->json($data, $data['code']);
    }
}
