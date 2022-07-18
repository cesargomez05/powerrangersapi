<?php

namespace App\Controllers;

use App\Traits\ControllerTrait;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Permission extends BaseResource
{
	use ResponseTrait, ControllerTrait;

	protected $modelName = 'App\Models\PermissionModel';

	/**
	 * @var \App\Models\PermissionModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function create($userId)
	{
		// Datos de entrada de la petición
		$checkRequestData = $this->checkRequestData($postData, $postFiles);
		if (isset($checkRequestData)) {
			return $checkRequestData;
		}
		$this->addSegmentProperties($postData, ['username' => $userId]);

		// Se valida los datos de la petición
		$validateRecord = $this->model->validateRecord($postData, $postFiles, 'post');
		if ($validateRecord !== true) {
			return $this->getResponse(400, $validateRecord);
		}

		$result = $this->model->insertRecord($postData);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->getResponse(500, $result);
		}

		return $this->success("Record successfully created", 201);
	}
}
