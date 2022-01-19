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
use Config\Services;

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
				self::throwError(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Method not available');
		}

		// Se valida los parámetros definidos en la ruta
		if (!isset($arguments)) {
			self::throwError(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Invalid resource filter parameters');
		}

		// Se obtiene el Id del recurso
		$moduleId = isset($arguments[0]) ? $arguments[0] : '';
		if (!$moduleId) {
			return self::throwError(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Invalid module parameter');
		}

		// Se valida si el módulo a ejecutar corresponde a aquellos protegidos solamente para el usuario 'root'
		if (in_array($moduleId, ['module', 'user', 'permission'])) {
			// Se ejecuta la validación OAuth2 para obtener el 'username' del usuario que accede con el token
			$this->validateOAuth2Authentication($username);
			// Se valida si el usuario autenticado corresponde al usuario 'root'
			if ($username == 'root') {
				return;
			} else {
				return self::throwError(ResponseInterface::HTTP_UNAUTHORIZED, 'User not authorized for access this resource');
			}
		}

		// Se consulta los datos del módulo en la base de datos
		$moduleModel = new ModuleModel();
		$module = $moduleModel->find($moduleId);
		if (!$module) {
			return self::throwError(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Resource not found');
		}

		// Se valida que exista algún tipo de autorización
		if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
			return self::throwError(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Authorization type not found');
		}

		// Se valida a través de la propiedad '$_SERVER['HTTP_AUTHORIZATION']' el tipo de autorización
		if ((bool) preg_match('/^Basic/', $_SERVER['HTTP_AUTHORIZATION'])) {
			// Se valida la autenticación Basic (usuario y contraseña)
			self::validateBasicAuthentication($username);
		} elseif ((bool) preg_match('/^Bearer/', $_SERVER['HTTP_AUTHORIZATION'])) {
			// Se valida la autenticación con OAuth2
			self::validateOAuth2Authentication($username);
		} else {
			// Se valida la autenticación con JWT (JSON Web Token)
			self::validateJWTAuthentication($_SERVER['HTTP_AUTHORIZATION'], $username);
		}

		// Se consulta los permisos asociados al usuario autenticado y recurso
		$permissionModel = new PermissionModel();
		if (!$permissionModel->checkPermissions($username, $moduleId, $permission)) {
			return self::throwError(ResponseInterface::HTTP_FORBIDDEN, 'Cannot have permissions for ' . $permission . ' records in this resource');
		}
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
		// Not apply action after filter
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
			AuthFilter::throwError(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Invalid JWT value');
		}
	}

	protected static function throwError($code, $message)
	{
		return Services::response()->setStatusCode($code)->setJSON(['status' => $code, 'message' => $message]);
	}
}
