<?php

namespace App\Controllers;

use App\Traits\ControllerTrait;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Ranger extends BaseResource
{
	use ResponseTrait, ControllerTrait;

	protected $modelName = 'App\Models\RangerModel';

	/**
	 * @var \App\Models\RangerModel
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

		// Se "mueve" los archivos subidos a la respectiva carpeta
		move_files($postData);
		move_files($postData['morpher']);
		unset($postData['morpher']);

		return $this->respondCreated($postData);
	}

	public function update($id)
	{
		// Datos de entrada de la petición
		$checkRequestData = $this->checkRequestData($postData, $postFiles, $method);
		if (isset($checkRequestData)) {
			return $checkRequestData;
		}
		// Se elimina las propiedades asociadas al morpher en la actualización de datos del ranger
		unset($postData['morpherId']);
		unset($postData['morpher']);
		unset($postFiles['morpher']);

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
		move_files($postData['morpher']);

		return $this->success("Record successfully updated");
	}
}
