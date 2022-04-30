<?php

namespace App\Libraries;

use Firebase\JWT\JWT;

class JsonWebToken
{
	protected $key = 'WWtiReEBpnHAx4USVX2iwXqYPzdYlf76';
	protected $alg = 'HS256';

	public function generate($username)
	{
		$iat = time();
		$nbf = $iat + 10;
		$exp = $iat + 3600;

		$payload = array(
			"iss" => "powerrangersapi",
			"aud" => "powerrangersapi",
			"iat" => $iat, // issued at
			"nbf" => $nbf, //not before in seconds
			"exp" => $exp, // expire time in seconds
			"username" => $username
		);

		return JWT::encode($payload, $this->key, $this->alg);
	}

	public function decryptToken($token, &$username)
	{
		$decoded = JWT::decode($token, $this->key, [$this->alg]);
		$username = $decoded->username;
	}
}
