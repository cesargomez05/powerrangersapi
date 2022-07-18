<?php

namespace App\Libraries;

use OAuth2\Request;

class OAuth
{
	/**
	 * @var \OAuth2\Server
	 */
	public $server;

	/**
	 * @var \OAuth2\Storage\Pdo
	 */
	protected $storage;

	public function __construct()
	{
		$this->storage = new \OAuth2\Storage\Pdo([
			'dsn' => getenv('database.default.DSN'),
			'username' => getenv('database.default.username'),
			'password' => getenv('database.default.password')
		]);
		$this->server = new \OAuth2\Server($this->storage);
		$this->server->addGrantType(new \OAuth2\GrantType\ClientCredentials($this->storage, ['allow_credentials_in_request_body' => false]));
		$this->server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($this->storage));
		$this->server->addGrantType(new \OAuth2\GrantType\UserCredentials($this->storage));
		$this->server->addGrantType(new \OAuth2\GrantType\RefreshToken($this->storage, ['always_issue_new_refresh_token' => true]));
	}

	public function generateOAuth2(&$code, &$body)
	{
		$respond = $this->server->handleTokenRequest(Request::createFromGlobals());

		$code = $respond->getStatusCode();
		$body = $respond->getResponseBody();
	}

	public function validateOAuth2(&$username)
	{
		$request = Request::createFromGlobals();

		if (!$this->server->verifyResourceRequest($request)) {
			return $this->server->getResponse();
		}

		// Obtiene el dato del usuario autenticado con el Token
		$token = $this->server->getAccessTokenData(\OAuth2\Request::createFromGlobals());
		$username = $token['user_id'];
	}
}
