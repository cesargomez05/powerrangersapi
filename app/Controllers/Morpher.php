<?php

namespace App\Controllers;

use App\Traits\ControllerTrait;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Morpher extends BaseResource
{
	use ResponseTrait, ControllerTrait;

	protected $modelName = 'App\Models\MorpherModel';

	/**
	 * @var \App\Models\MorpherModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function create()
	{
		// Datos de entrada de la petición
		$checkRequestData = $this->checkRequestData($postData, $postFiles);
		if (isset($checkRequestData)) {
			return $checkRequestData;
		}

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

		// Se "mueve" el archivo subido a la respectiva carpeta
		move_files($postData);

		// Se elimina la lista de Id de rangers
		unset($postData['rangersId']);

		return $this->respondCreated($postData);
	}

	public function update($id)
	{
		// Datos de entrada de la petición
		$checkRequestData = $this->checkRequestData($postData, $postFiles, $method);
		if (isset($checkRequestData)) {
			return $checkRequestData;
		}

		// Se elimina la lista de rangers en la actualización de información del morpher
		unset($postData['rangersId']);

		// Se valida los datos de la petición
		$validateRecord = $this->model->validateRecord(
			$postData,
			$postFiles,
			$method,
			$this->model->get($id)->toArray()
		);
		if ($validateRecord !== true) {
			return $this->getResponse(400, $validateRecord);
		}

		$result = $this->model->updateRecord($postData, $id);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->getResponse(500, $result);
		}

		// Se "mueve" el archivo subido a la respectiva carpeta
		move_files($postData);

		return $this->success("Record successfully updated");
	}
}
