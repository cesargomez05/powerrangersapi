<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Permission extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\PermissionModel';

	/**
	 * @var \App\Models\PermissionModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function index($userId)
	{
	}

	public function create($userId)
	{
	}

	public function delete($userId, $moduleId)
	{
	}
}
