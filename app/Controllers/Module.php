<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Module extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\ModuleModel';

	/**
	 * @var \App\Models\ModuleModel
	 */
	protected $model;

	protected $helpers = ['app'];

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
}
