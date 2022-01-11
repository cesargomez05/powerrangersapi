<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class MegazordZord extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\MegazordZordModel';

	public function index($megazordId)
	{
	}

	public function create($megazordId)
	{
	}

	public function delete($megazordId, $zordId)
	{
	}

	/*
	// Atributos de la clase APIController
	protected $existsRecordMessage = 'The record information is used by other record in this season';
	protected $parentRecordNotFoundMessage = 'Season id not found';*/
}
