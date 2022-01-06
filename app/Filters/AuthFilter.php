<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

use App\Libraries\OAuth;
use App\Libraries\JsonWebToken;
use App\Models\ModuleModel;
use App\Models\PermissionModel;
use App\Models\UserModel;

class AuthFilter implements FilterInterface
{
	public function before(RequestInterface $request, $arguments = null)
	{
		// Se valida el método con el que es invocado el API Rest, para saber la acción a validar
		switch ($request->getMethod()) {
			case 'get':
				$permission = 'read';
				break;
			case 'post':
				$permission = 'create';
				break;
			case 'put':
			case 'patch':
				$permission = 'update';
				break;
			case 'delete':
				$permission = 'delete';
				break;
			default:
				header('Content-type: application/json');
				die(json_encode(['status' => 500, 'message' => 'Method not available']));
		}

		// Se valida los parámetros definidos en la ruta
		if (!isset($arguments)) {
			$this->throwError(500, 'Invalid resource filter parameters');
		}

		// Se obtiene el Id del recurso
		$moduleId = isset($arguments[0]) ? $arguments[0] : '';
		if (!$moduleId) {
			$this->throwError(500, 'Invalid module parameter');
		}

		// Se valida si el módulo a ejecutar corresponde a aquellos protegidos solamente para el usuario 'root'
		if (in_array($moduleId, ['module', 'user', 'permission'])) {
			// Se ejecuta la validación OAuth2 para obtener el 'username' del usuario que accede con el token
			$this->validateOAuth2Authentication($username);
			// Se valida si el usuario autenticado corresponde al usuario 'root'
			if ($username == 'root') {
				return;
			} else {
				$this->throwError(401, 'User not authorized for access this resource');
			}
		}

		// Se consulta los datos del módulo en la base de datos
		$moduleModel = new ModuleModel();
		$module = $moduleModel->find($moduleId);
		if (!$module) {
			$this->throwError(500, 'Resource not found');
		}

		// Se valida que exista algún tipo de autorización
		if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
			$this->throwError(500, 'Authorization type not found');
		}

		// Se valida a través de la propiedad '$_SERVER['HTTP_AUTHORIZATION']' el tipo de autorización
		if ((bool) preg_match('/^Basic/', $_SERVER['HTTP_AUTHORIZATION'])) {
			// Se valida la autenticación Basic (usuario y contraseña)
			$this->validateBasicAuthentication($username);
		} elseif ((bool) preg_match('/^Bearer/', $_SERVER['HTTP_AUTHORIZATION'])) {
			// Se valida la autenticación con OAuth2
			$this->validateOAuth2Authentication($username);
		} else {
			// Se valida la autenticación con JWT (JSON Web Token)
			$this->validateJWTAuthentication($_SERVER['HTTP_AUTHORIZATION'], $username);
		}

		// Se consulta los permisos asociados al usuario autenticado y recurso
		$permissionModel = new PermissionModel();
		if (!$permissionModel->checkPermissions($username, $moduleId, $permission)) {
			$this->throwError(403, 'Cannot have permissions for ' . $permission . ' records in this resource');
		}
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
	}

	public static function validateOAuth2Authentication(&$username)
	{
		$oauth = new OAuth();
		$oauth->validateOAuth2($username);
	}

	public static function validateBasicAuthentication(&$username)
	{
		// Usuario y contraseña de autenticación
		$username = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
		$password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';

		// Se valida si se definió un nombre de usuario
		if (!$username) {
			AuthFilter::throwError(400, 'The username is required');
		}

		// Se consulta el usuario
		$userModel = new UserModel();
		$user = $userModel->checkUser($username, $password);
		if (!$user) {
			AuthFilter::throwError(401, 'Invalid credentials');
		}
	}

	public static function validateJWTAuthentication($token, &$username)
	{
		try {
			$jsonWebToken = new JsonWebToken();
			$jsonWebToken->decryptToken($token, $username);
		} catch (\Exception $ex) {
			AuthFilter::throwError(500, 'Invalid JWT value');
		}
	}

	protected static function throwError($code, $message)
	{
		header('Content-type: application/json');
		header('HTTP/1.1 ' . $code);
		die(json_encode(['status' => $code, 'message' => $message]));
	}
}
