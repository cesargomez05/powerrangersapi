<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Permission extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\PermissionModel';

	public function index($userId)
	{
	}

	public function create($userId)
	{
	}

	public function delete($userId, $moduleId)
	{
	}

	/*
	// Atributos de la clase APIController
	protected $existsRecordMessage = 'The permission information is used by other record in this season';
	protected $parentRecordNotFoundMessage = 'User id not found';

	protected function checkParentRecord($ids, $isUpdate = false)
	{
		// Se valida los datos del usuario
		$userModel = new UserModel();
		$user = $userModel->getRecord($isUpdate ? array_slice($ids, 0, 1) : $ids);
		return (bool) $user !== false;
	}*/
}
