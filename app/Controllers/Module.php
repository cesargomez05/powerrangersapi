<?php

namespace App\Controllers;

use App\Models\PermissionModel;

class Module extends APIController
{
	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\ModuleModel';

	protected function validateDeleteRecord($id)
	{
		$errors = [];

		$model = new PermissionModel();
		if ($model->checkRecordsByForeignKey(['moduleId' => $id])) {
			$errors['permissions'] = "The module has one or many permissions records";
		}

		return count($errors) ? $errors : TRUE;
	}
}
