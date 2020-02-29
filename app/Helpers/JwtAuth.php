<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth {

	public $key;

	public function __construct() {
		$this->key = 'kitne';
	}

	public function singup($username, $password, $getToken = null) {
		// Buscar si existe el usuario con las credenciales
		$user = User::where([
			'username' => $username,
			'password' => $password,
		])->first();

		// Comprobrar si son correctas
		$singup = false;
		if(is_object($user)) {
			$singup = true;
		}

		// Generar el token con los datos del usuario identificado
		if($singup) {
			$token = array(
				'sub'		=> $user->id,
				'email'		=> $user->email,
				'name'		=> $user->name,
				'lastName'	=> $user->last_name,
				'username'	=> $user->username,
				'iat'		=> time(),
				'exp'		=> time() + (7 * 24 * 60 * 60),
			);

			$jwt = JWT::encode($token, $this->key, 'HS256');
			$decoded = JWT::decode($jwt, $this->key, ['HS256']);

			if(is_null($getToken)) {
				$data = $jwt;
			} else {
				$data = $decoded;
			}

		} else {
			$data = array(
				'status' => 'error',
				'message' => 'Login incorrecto'
			);
		}


		// Devolver los datos decodificados o el token, en función de un parametro

		return $data;
	}

	public function checkToken($jwt, $getIdentity = false) {
		$auth = false;

		try {
			$jwt = str_replace('"', '', $jwt);
			$decoded = JWT::decode($jwt, $this->key, ['HS256']);
		} catch(\UnexpectedValueException $e) {
			$auth = false;
		} catch(\DomainException $e) {
			$auth = false;
		}

		if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
			$auth = true;
		} else {
			$auth = false;
		}

		if($getIdentity) {
			return $decoded;
		}

		return $auth;
	}
}