<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Module extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\ModuleModel';

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
	protected function validateDeleteRecord($id)
	{
		$errors = [];

		$model = new PermissionModel();
		if ($model->checkRecordsByForeignKey(['moduleId' => $id])) {
			$errors['permissions'] = "The module has one or many permissions records";
		}

		return count($errors) ? $errors : true;
	}*/
}
