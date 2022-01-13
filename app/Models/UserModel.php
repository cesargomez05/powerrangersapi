<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class UserModel extends Model
{
	use ModelTrait;

	protected $table = 'oauth_users';

	protected $primaryKey = 'username';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['username', 'password', 'first_name', 'last_name', 'email'];
	protected $validationRules = [
		'username' => 'required|max_length[80]|is_unique[oauth_users.username,username,{_username}]',
		'password' => 'required|min_length[8]|max_length[20]|validate_password',
		'first_name' => 'permit_empty|max_length[80]',
		'last_name' => 'permit_empty|max_length[80]',
		'email' => 'permit_empty|valid_email|max_length[80]'
	];
	protected $validationMessages = [
		'username' => [
			'is_unique' => 'The {field} value is used by other user'
		],
		'password' => [
			'validate_password' => 'The password must be between 8 and 20 characters, at least 1 Uppercase and Lowercase character, 1 digit and 1 of the following characters: !@#$%^&*-'
		],
		'newPassword' => [
			'validate_password' => 'The new password must be between 8 and 20 characters, at least 1 Uppercase and Lowercase character, 1 digit and 1 of the following characters: !@#$%^&*-'
		]
	];
	protected $returnType = 'App\Entities\User';

	public function validateRecord(&$filesData, $property, &$postData, $postFiles, $ids, $method, $record = null, $nodes = [])
	{
		// Se valida si el proceso corresponde a un nuevo registro de serie
		if ($method == 'post') {
			// Se valida si existe datos de al menos 1 permiso
			if (isset($postData['permissions'])) {
				if (!is_array($postData['permissions'])) {
					return ['permissions' => 'The permissions value is not a array'];
				}

				// Se invoca la clase Model asociada a los permisos; y se omite la validación del Id del usuario
				$permissionsModel = new PermissionModel();
				$permissionsModel->removeValidationRule('username');

				// Se recorre la lista de permisos para validar los datos de cada uno de ellos
				$errors = [];
				$modulesId = [];
				foreach ($postData['permissions'] as $key => &$value) {
					// Se ejecuta la validación de los datos de cada uno de los permisos
					$validRecord = $permissionsModel->validateRecord($filesData['permissions'], $key, $value, $postFiles, [], 'post', null, array_merge($nodes, ['permissions', $key]));
					if ($validRecord !== true) {
						$errors[$key] = $validRecord;
					} else {
						if (in_array($value['moduleId'], $modulesId)) {
							$errors[$key] = 'The moduleId is used by other permission record';
						} else {
							array_push($modulesId, $value['moduleId']);
						}
					}
				}
				if (count($errors)) {
					return ['permissions' => $errors];
				}
			}
		}
		$response = parent::validateRecord($filesData, $property, $postData, $postFiles, $ids, $method, $record, $nodes);
		if ($response === true) {
			if ($method == 'post') {
				// Se encripta la contraseña usando MD5
				$postData['password'] = sha1($postData['password']);
			} elseif (in_array($method, ['put', 'patch'])) {
				if (isset($postData['password'])) {
					// Se verifica las credenciales del usuario, validando con la contraseña previamente ingresada
					$user = $this->checkUser($record->username, $postData['password']);
					if ($user == false) {
						return ['user' => 'Las credenciales de acceso son inválidas'];
					} else {
						// Se establece la nueva contraseña
						$postData['password'] = sha1($postData['newPassword']);
					}
				}
			}
		}
		return $response;
	}

	public function validateRecordFields(&$record, $ids, $method, $prevRecord = null): bool
	{
		// Se define los datos de la nueva contraseña (si aplica)
		if (in_array($method, ['put', 'patch'])) {
			if (isset($record['newPassword']) || isset($record['confirmPassword'])) {
				if (!isset($record['password'])) {
					$record['password'] = '';
				}
			}
			if (!isset($record['newPassword'])) {
				$record['newPassword'] = '';
			}
			if (!isset($record['confirmPassword'])) {
				$record['confirmPassword'] = '';
			}
		}
		return parent::validateRecordFields($record, $ids, $method, $prevRecord);
	}

	public function insertRecord(&$record)
	{
		// Se ejecuta el método para insertar el registro
		$user = parent::insertRecord($record);
		if (!isset($user['error'])) {
			// Se elimina el valor de la contraseña (si no hubo error)
			unset($record['password']);
		}
	}

	public function checkUser($username, $password)
	{
		// Se establece la consulta para obtener el Id del usuario cuyo username y contraseña correspondan al usuario autenticado
		$builder = $this->builder();
		$builder->select($this->primaryKey);
		$builder->where('username', $username);
		if (isset($password) && !empty($password)) {
			$builder->where('password', sha1($password));
		}

		// Se ejecuta la consulta y se valida si esta retornó algun resultado
		$query = $builder->get();
		$row = $query->getRow();
		if ($row !== NULL) {
			return $row;
		}
		return false;
	}
}
