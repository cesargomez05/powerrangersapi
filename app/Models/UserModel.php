<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class UserModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
	}

	protected $table = 'oauth_users';

	protected $primaryKey = 'username';

	protected $allowedFields = ['username', 'password', 'first_name', 'last_name', 'email'];

	protected $nestedModelClasses = [
		'permissionModel' => 'App\Models\PermissionModel'
	];

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

	protected $rulesId = 'required|max_length[80]';

	protected function setRecordsCondition($query)
	{
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('first_name', $query['q'], 'both');
			$this->orLike('last_name', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($id)
	{
		$this->where('username', $id);
	}

	public function insertRecord(&$record)
	{
		$this->db->transBegin();

		$this->setValidationRule('password', 'permit_empty');
		$this->setValidationRule('confirmPassword', 'permit_empty');

		// Se procede a insertar el registro en la base de datos
		$result = $this->insertRecordTrait($record);
		if ($result !== true) {
			$this->db->transRollback();
			return $result;
		}

		if (isset($record['permissions']) && count($record['permissions']) > 0) {
			$permissionModel = model($this->nestedModelClasses['permissionModel']);
			$permissionResult = $permissionModel->insertPermissions($record['username'], $record['permissions']);
			if ($permissionResult === false) {
				$this->db->transRollback();
				return $permissionModel->errors();
			}
		}

		$this->db->transCommit();

		return true;
	}

	protected function checkNestedRecords($id, &$errors)
	{
		$permissionModel = model($this->nestedModelClasses['permissionModel']);
		if ($permissionModel->countByUserId($id)) {
			$errors['permission'] = 'There are nested permission records to user';
		}
	}

	public function validateRecord(&$postData, $postFiles, $method, $prevRecord = null)
	{
		$errors = [];

		if ($method == 'post') {
			$this->setValidationRule('confirmPassword', 'required_with[password]|matches[password]');
		}
		if (in_array($method, ['put', 'patch'])) {
			if (!isset($postData['username']) || empty($postData['username'])) {
				$postData['username'] = $prevRecord['username'];
			}
			if (!isset($postData['password']) || empty($postData['password'])) {
				$this->setValidationRule('password', 'permit_empty');
			}

			$this->setValidationRule('newPassword', 'required_with[password]|max_length[100]|validate_password');
			$this->setValidationRule('confirmPassword', 'required_with[password]|matches[newPassword]');
		}

		$this->validateRecordProperties($postData, $method, $prevRecord);

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		// Se valida si no hubo error de validación en el campo de contraseña, para establecer el respectivo valor encriptado
		if (!isset($errors['password'])) {
			if (isset($postData['password'])) {
				$postData['password'] = password_hash($postData['password'], PASSWORD_BCRYPT);
			} else {
				unset($postData['password']);
			}
		}

		if ($method == 'post' && isset($postData['permissions'])) {
			if (!is_array($postData['permissions'])) {
				$errors = array_merge(['permissions' => 'The permissions value is not a array'], $errors);
			} else {
				$permissionModel = model($this->nestedModelClasses['permissionModel']);
				$permissionModel->setValidationRule('username', 'permit_empty');

				$permissionsErrors = [];
				$modulesId = [];

				foreach ($postData['permissions'] as $i => $permission) {
					$permissionErrors = $permissionModel->validateRecord($permission, [], 'post');
					if ($permissionErrors !== true) {
						$permissionsErrors[$i] = $permissionErrors;
					} else {
						$moduleId = $permission['moduleId'];
						if (in_array($moduleId, $modulesId)) {
							$permissionsErrors[$i] = ['moduleId' => 'The module id is used by other record'];
						} else {
							$modulesId[] = $moduleId;
						}
					}
				}

				if (!empty($permissionsErrors)) {
					$errors['permissions'] = $permissionsErrors;
				}
			}
		}

		return empty($errors) ? true : $errors;
	}

	public function checkUser($username, $password)
	{
		// Se establece la consulta para obtener el Id del usuario cuyo username y contraseña correspondan al usuario autenticado
		$builder = $this->builder();
		$builder->select(['password']);
		$builder->where('username', $username);

		// Se ejecuta la consulta y se valida si esta retornó algun resultado
		$query = $builder->get();
		$row = $query->getRow();
		if ($row !== NULL && password_verify($password, $row->password)) {
			return $row;
		}
		return false;
	}
}
