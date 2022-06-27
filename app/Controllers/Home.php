<?php

namespace App\Controllers;

use App\Filters\AuthFilter;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\JsonWebToken;
use App\Libraries\OAuth;

class Home extends BaseController
{
	use ResponseTrait;

	public function index()
	{
		return view('welcome_message');
	}

	public function getOAuth2()
	{
		$oauth = new OAuth();
		$oauth->generateOAuth2($code, $body);
		return $this->respond(json_decode($body), $code);
	}

	public function getJwt()
	{
		// Se ejecuta la autenticación Basic para validar el usuario que se autentica
		$result = AuthFilter::validateBasicAuthentication($username);
		if (isset($result)) {
			return $result;
		}

		// Se genera el token de acceso para el usuario autenticado
		$jsonWebToken = new JsonWebToken();
		$token = $jsonWebToken->generate($username);

		// Se retorna el token generado
		return $this->respond(['token' => $token], 200);
	}
}
