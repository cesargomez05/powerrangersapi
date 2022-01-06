<?php

namespace App\Models;

class PermissionModel extends APIModel
{
	// Atributos de la clase APIModel
	protected $columnValue = '';

	// Atributos de la clase Model
	protected $table = 'permissions';
	protected $primaryKeys = ['username', 'moduleId'];

	// Atributos de la clase BaseModel
	protected $allowedFields = ['username', 'moduleId', 'create', 'read', 'update', 'delete'];
	protected $validationRules = [
		'username' => 'required|is_natural_no_zero|exists_id[oauth_users.username]',
		'moduleId' => 'required|alpha_numeric|exists_id[modules.id]',
		'create' => 'required|is_natural|in_list[0,1]',
		'read' => 'required|is_natural|in_list[0,1]',
		'update' => 'required|is_natural|in_list[0,1]',
		'delete' => 'required|is_natural|in_list[0,1]'
	];

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
		return FALSE;
	}
}
