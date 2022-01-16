<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class PermissionModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
	}

	protected $table = 'permissions';

	protected $allowedFields = ['username', 'moduleId', 'create', 'read', 'update', 'delete'];

	protected $validationRules = [
		'username' => 'required|alpha_numeric|exists_id[oauth_users.username]',
		'moduleId' => 'required|alpha_numeric|exists_id[modules.id]',
		'create' => 'required|is_natural|in_list[0,1]',
		'read' => 'required|is_natural|in_list[0,1]',
		'update' => 'required|is_natural|in_list[0,1]',
		'delete' => 'required|is_natural|in_list[0,1]'
	];

	protected function setRecordsCondition($query, $username)
	{
		$this->setTable('view_permissions');

		$this->where('username', $username);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('moduleName', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($username, $moduleId)
	{
		$this->where('username', $username)
			->where('moduleId', $moduleId);
	}

	public function insertRecord(&$record)
	{
		$prevRecord = $this->get($record['username'], $record['moduleId']);
		if (isset($prevRecord)) {
			return 'The permission has been assigned to user';
		}

		// Se procede a insertar el registro en la base de datos
		return $this->insertRecordTrait($record);
	}

	public function validateRecord(&$postData, $postFiles, $method, $prevRecord = null)
	{
		$errors = [];

		$this->validateRecordProperties($postData, $method, $prevRecord);

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return count($errors) > 0 ? $errors : true;
	}

	public function checkPermissions($username, $moduleId, $permission)
	{
		// Se ejecuta la consulta del permiso
		$builder = $this->builder();
		$builder->select("$permission as available");
		$builder->where('username', $username);
		$builder->where('moduleId', $moduleId);

		// Se ejecuta la consulta del registro
		$query = $builder->get();
		$row = $query->getRow();
		if ($row !== NULL) {
			return filter_var($row->available, FILTER_VALIDATE_BOOLEAN);
		}
		return false;
	}

	public function insertPermissions($username, $permissions)
	{
		foreach ($permissions as &$permission) {
			$permission['username'] = $username;
		}

		return $this->insertBatch($permissions);
	}
}
