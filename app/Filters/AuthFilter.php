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
use App\Traits\FilterTrait;
use Config\Services;

class AuthFilter implements FilterInterface
{
	use FilterTrait;

	public function before(RequestInterface $request, $arguments = null)
	{
		// Se valida los parámetros de entrada para obtener la accion y módulo a consultar
		$checkInputParams = self::checkInputParams($request, $arguments, $method, $moduleId, $permission);
		if (isset($checkInputParams)) {
			return $checkInputParams;
		}

		// Se valida si el módulo a ejecutar corresponde a aquellos protegidos solamente para el usuario 'root'
		if (in_array($moduleId, ['module', 'user', 'permission'])) {
			// Se ejecuta la validación OAuth2 para obtener el 'username' del usuario que accede con el token
			$result = self::validateOAuth2Authentication();
			if (isset($result)) {
				return $result;
			}
		} else {
			$checkModulePermissions = self::checkModulePermissions($method, $moduleId, $permission);
			if (isset($checkModulePermissions)) {
				return $checkModulePermissions;
			}
		}
	}

	/**
	 * Valida los parámetros de entrada requeridos para ejecutar alguna petición
	 * @param $request Objeto de datos de la petición.
	 * @param $arguments Parámetro con los argumentos definidos en la ruta.
	 * @param string $permission Valor con el nombre de la acción a ejecutar.
	 * @param $moduleId Id del módulo a validar.
	 */
	private static function checkInputParams($request, $arguments, &$method, &$moduleId, &$permission)
	{
		// Se valida el método con el que es invocado el API Rest, para saber la acción a validar
		$method = $request->getMethod();
		$permission = [
			'get' => 'read',
			'post' => 'create',
			'put' => 'update',
			'patch' => 'update',
			'delete' => 'delete'
		][$method];
		if (!isset($permission)) {
			return self::throwError(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Method not available');
		}

		// Se valida los parámetros definidos en la ruta
		if (!isset($arguments)) {
			return self::throwError(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Invalid resource filter parameters');
		}

		// Se obtiene el Id del recurso
		$moduleId = isset($arguments[0]) ? $arguments[0] : '';
		if (!$moduleId) {
			return self::throwError(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Invalid module parameter');
		}
	}

	private static function checkModulePermissions($method, $moduleId, $permission)
	{
		// Se consulta los datos del módulo en la base de datos
		$moduleModel = new ModuleModel();
		$module = $moduleModel->find($moduleId);
		if (!$module) {
			return self::throwError(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Resource not found');
		}

		// Validación de si se definió (true) o no (false) un tipo de autenticación
		$hasAuthenticationOption = isset($_SERVER['HTTP_AUTHORIZATION']);

		// Variable de sesión donde se indica si la consulta de registros corresponde a una consulta pública
		$session = Services::session();
		$session->remove('type');

		// Se valida si se definió un tipo de autenticación
		if ($hasAuthenticationOption) {
			$validateAuthentication = self::validateAuthentication($moduleId, $permission);
			if (isset($validateAuthentication)) {
				return $validateAuthentication;
			}
		} else {
			// Se verifica si el tipo de la petición no corresponde a un tipo GET
			if ($method !== 'get') {
				return self::throwError(ResponseInterface::HTTP_UNAUTHORIZED, 'Action not authorized');
			}

			$session->set(['type' => 'public']);
			$session->markAsFlashdata('type');
		}
	}

	private static function validateOAuth2Authentication()
	{
		$oauth = new OAuth();
		$response =  $oauth->validateOAuth2($username);
		if ($response) {
			return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, $response->getParameter('error_description'));
		}

		// Se valida si el usuario autenticado corresponde al usuario 'root'
		if ($username !== 'root') {
			return self::throwError(ResponseInterface::HTTP_UNAUTHORIZED, 'User not authorized for access this resource');
		}
	}

	private static function validateAuthentication($moduleId, $permission)
	{
		// Se valida a través de la propiedad '$_SERVER['HTTP_AUTHORIZATION']' el tipo de autorización
		if ((bool) preg_match('/^Basic/', $_SERVER['HTTP_AUTHORIZATION'])) {
			// Se valida la autenticación Basic (usuario y contraseña)
			$result = self::validateBasicAuthentication($username);
		} elseif ((bool) preg_match('/^Bearer (.+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
			// Se valida la autenticación con JWT (JSON Web Token)
			$result = self::validateJWTAuthentication($matches[1], $username);
		}
		if (isset($result)) {
			return $result;
		}

		if (!isset($username)) {
			return self::throwError(ResponseInterface::HTTP_UNAUTHORIZED, 'Access not authorized');
		}

		// Se consulta los permisos asociados al usuario autenticado y recurso
		$permissionModel = new PermissionModel();
		if (!$permissionModel->checkPermissions($username, $moduleId, $permission)) {
			return self::throwError(ResponseInterface::HTTP_FORBIDDEN, 'Cannot have permissions for ' . $permission . ' records in this resource');
		}
	}

	public static function validateBasicAuthentication(&$username)
	{
		// Usuario y contraseña de autenticación
		$username = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
		$password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';

		// Se valida si se definió un nombre de usuario
		if (!$username) {
			return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, 'The username is required');
		}

		// Se consulta el usuario
		$userModel = new UserModel();
		$user = $userModel->checkUser($username, $password);
		if (!$user) {
			return self::throwError(ResponseInterface::HTTP_UNAUTHORIZED, 'Invalid credentials');
		}
	}

	private static function validateJWTAuthentication($token, &$username)
	{
		try {
			$jsonWebToken = new JsonWebToken();
			$jsonWebToken->decryptToken($token, $username);
		} catch (\Exception $ex) {
			return self::throwError(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'Invalid JWT value');
		}
	}
}
