<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class MegazordZord extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\MegazordZordModel';

	/**
	 * @var \App\Models\MegazordZordModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function index($megazordId)
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		$megazordZords = $this->model->list($filter, $megazordId);
		return $this->respond($megazordZords);
	}

	public function create($megazordId)
	{
		// Datos de entrada de la petición
		$postData = $this->request->getPost();
		$postFiles = $this->request->getFiles();

		// Se valida si no existen datos enviados por método POST
		if (empty($postData) && empty($postFiles)) {
			return $this->fail('Please define the data to be recorded');
		}

		$postData['megazordId'] = $megazordId;

		// Se valida los datos de la petición
		$validateRecord = $this->model->validateRecord($postData, $postFiles, 'post');
		if ($validateRecord !== true) {
			return $this->respond(['errors' => $validateRecord], 400);
		}

		$result = $this->model->insertRecord($postData);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		return $this->success("Record successfully created", 201);
	}

	public function delete($megazordId, $zordId)
	{
		$result = $this->model->deleteRecord($megazordId, $zordId);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		return $this->success("Record successfully deleted");
	}
}
