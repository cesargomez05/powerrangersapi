<?php

namespace App\Controllers;

use App\Models\PermissionModel;

class User extends APIController
{
	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\UserModel';

	protected function validateDeleteRecord($username)
	{
		$errors = [];

		$model = new PermissionModel();
		if ($model->checkRecordsByForeignKey(['username' => $username])) {
			$errors['permissions'] = "The user has one or many permissions records";
		}

		return count($errors) ? $errors : TRUE;
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

		/*
		// Se inserta los datos de los permisos asociados al usuario (si aplica)
		if (isset($postData['permissions'])) {
			// Se establece el Id del usuario en cada uno de los permisos
			foreach ($postData['permissions'] as $key => &$permission) {
				$permission['username'] = $user['primaryKey'];
			}

			// Se ejecuta el proceso de inserción de los datos
			$permissionModel = new PermissionModel();
			$result = $permissionModel->insertBatch($postData['permissions']);
			if ($result === FALSE) {
				$this->db->transRollback();
				return ['permissions' => $permissionModel->errors()];
			}
		}*/

		// Se finaliza la transacción
		$this->model->db->transCommit();

		// Se retorna TRUE para indicar que la función se ejecutó correctamente
		return TRUE;
	}
}
