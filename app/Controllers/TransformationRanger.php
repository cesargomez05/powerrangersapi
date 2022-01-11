<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class TransformationRanger extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\TransformationRangerModel';

	public function index($transformationId)
	{
	}

	public function show($transformationId, $rangerId)
	{
	}

	public function create($transformationId)
	{
	}

	public function update($transformationId, $rangerId)
	{
	}

	public function delete($transformationId, $rangerId)
	{
	}
}
