<?php

namespace App\Controllers;

use App\Models\UserModel;

class Permission extends APIController
{
	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\PermissionModel';

	// Atributos de la clase APIController
	protected $existsRecordMessage = 'The permission information is used by other record in this season';
	protected $parentRecordNotFoundMessage = 'User id not found';

	protected function checkParentRecord($ids, $isUpdate = FALSE)
	{
		// Se valida los datos del usuario
		$userModel = new UserModel();
		$user = $userModel->getRecord($isUpdate ? array_slice($ids, 0, 1) : $ids);
		return (bool) $user !== FALSE;
	}
}
