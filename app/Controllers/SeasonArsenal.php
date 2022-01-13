<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class SeasonArsenal extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\SeasonArsenalModel';

	/**
	 * @var \App\Models\SeasonArsenalModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function index($serieId, $seasonNumber)
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		$seasonArsenalList = $this->model->list($serieId, $seasonNumber, $filter);
		return $this->respond($seasonArsenalList);
	}

	public function create($serieId, $seasonNumber)
	{
		// Datos de entrada de la petición
		$postData = $this->request->getPost();
		$postFiles = $this->request->getFiles();

		// Se valida si no existen datos enviados por método POST
		if (empty($postData) && empty($postFiles)) {
			return $this->fail('Please define the data to be recorded');
		}

		$postData['serieId'] = $serieId;
		$postData['seasonNumber'] = $seasonNumber;

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

	public function delete($serieId, $seasonNumber, $arsenalId)
	{
		$seasonZord = $this->model->get($serieId, $seasonNumber, $arsenalId);
		if (!isset($seasonZord)) {
			return $this->failNotFound('Record not found');
		}

		$result = $this->model->deleteRecord($serieId, $seasonNumber, $arsenalId);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		return $this->success("Record successfully deleted");
	}
}
