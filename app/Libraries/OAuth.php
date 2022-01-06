<?php

namespace App\Libraries;

use OAuth2\Request;

class OAuth
{
	/**
	 * @var \OAuth2\Server
	 */
	public $server;
	protected $storage;

	public function __construct()
	{
		$this->storage = new \OAuth2\Storage\Pdo([
			'dsn' => getenv('database.default.DSN'),
			'username' => getenv('database.default.username'),
			'password' => getenv('database.default.password')
		]);
		$this->server = new \OAuth2\Server($this->storage);
		// Usuario y contraseña
		$this->server->addGrantType(new \OAuth2\GrantType\UserCredentials($this->storage));
		//$this->server->addGrantType(new \OAuth2\GrantType\RefreshToken($this->storage));
		//$this->server->addGrantType(new \OAuth2\GrantType\ClientCredentials($this->storage));
	}

	public function generateOAuth2(&$code, &$body)
	{
		$request = new Request();

		$respond = $this->server->handleTokenRequest(
			$request->createFromGlobals()
		);

		$code = $respond->getStatusCode();
		$body = $respond->getResponseBody();
	}

	public function validateOAuth2(&$username)
	{
		$request = Request::createFromGlobals();

		if (!$this->server->verifyResourceRequest($request)) {
			$this->server->getResponse()->send();
			exit();
		}

		// Obtiene el dato del usuario autenticado con el Token
		$token = $this->server->getAccessTokenData(\OAuth2\Request::createFromGlobals());
		$username = $token['user_id'];
	}
}
