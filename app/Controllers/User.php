<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class User extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\UserModel';

	public function index()
	{
	}

	public function show($id)
	{
	}

	public function create()
	{
	}

	public function update($id)
	{
	}

	public function delete($id)
	{
	}

	/*
	protected function validateDeleteRecord($username)
	{
		$errors = [];

		$model = new PermissionModel();
		if ($model->checkRecordsByForeignKey(['username' => $username])) {
			$errors['permissions'] = "The user has one or many permissions records";
		}

		return count($errors) ? $errors : true;
	}

	protected function insertRecord(&$postData, $filesData)
	{
		// Se inicializa una transacción sobre la base de datos
		$this->model->db->transBegin();

		// Se procede a insertar los datos del usuario en la base de datos
		$user = $this->model->insertRecord($postData);
		if (isset($user['error'])) {
			// Se retorna los mensajes de error de la validación
			$this->model->db->transRollback();
			return $user['error'];
		}

		
		// Se inserta los datos de los permisos asociados al usuario (si aplica)
		if (isset($postData['permissions'])) {
			// Se establece el Id del usuario en cada uno de los permisos
			foreach ($postData['permissions'] as $key => &$permission) {
				$permission['username'] = $user['primaryKey'];
			}

			// Se ejecuta el proceso de inserción de los datos
			$permissionModel = new PermissionModel();
			$result = $permissionModel->insertBatch($postData['permissions']);
			if ($result === false) {
				$this->db->transRollback();
				return ['permissions' => $permissionModel->errors()];
			}
		}

		// Se finaliza la transacción
		$this->model->db->transCommit();

		// Se retorna true para indicar que la función se ejecutó correctamente
		return true;
	}*/
}
