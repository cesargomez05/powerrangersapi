<?php

namespace App\Controllers;

use App\Traits\ControllerTrait;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class RangerMorpher extends BaseResource
{
	use ResponseTrait, ControllerTrait;

	protected $modelName = 'App\Models\RangerMorpherModel';

	/**
	 * @var \App\Models\RangerMorpherModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function create($rangerId)
	{
		// Datos de entrada de la petición
		$postData = $this->request->getPost();
		$postFiles = $this->request->getFiles();

		// Se valida si no existen datos enviados por método POST
		if (empty($postData) && empty($postFiles)) {
			return $this->fail('Please define the data to be recorded');
		}

		// Se define el Id del ranger
		$postData['rangerId'] = $rangerId;

		// Se valida los datos de la petición
		$validateRecord = $this->model->validateRecord($postData, $postFiles, 'post');
		if ($validateRecord !== true) {
			return $this->respond(['errors' => $validateRecord], 400);
		}

		// Se valida la existencia de la asociación del morpher al ranger
		if ($this->model->check($rangerId)) {
			// Se actualiza el registro en base de datos (si este existe)
			$result = $this->model->updateRecord($postData, $rangerId);
			if ($result !== true) {
				// Se retorna un mensaje de error si las validaciones no se cumplen
				return $this->respond(['errors' => $result], 500);
			}
		} else {
			// Se inserta en base de datos el registro
			$result = $this->model->insertRecord($postData);
			if ($result !== true) {
				// Se retorna un mensaje de error si las validaciones no se cumplen
				return $this->respond(['errors' => $result], 500);
			}
		}

		// Se "mueve" los archivos subidos a la respectiva carpeta
		move_files($postData);

		return $this->respondCreated($postData);
	}

	public function listByMorpher($morpherSlug)
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		$records = $this->model->listByMorpher($filter, $morpherSlug);
		return $this->respond($records);
	}
}
