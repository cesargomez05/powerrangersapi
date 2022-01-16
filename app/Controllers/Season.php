<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Season extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\SeasonModel';

	/**
	 * @var \App\Models\SeasonModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function index($serieId)
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		$seasons = $this->model->list($filter, $serieId);
		return $this->respond($seasons);
	}

	public function show($serieId, $number)
	{
		$season = $this->model->get($serieId, $number);
		return $this->respond(['record' => $season]);
	}

	public function create($serieId)
	{
		// Datos de entrada de la petición
		$postData = $this->request->getPost();
		$postFiles = $this->request->getFiles();

		// Se valida si no existen datos enviados por método POST
		if (empty($postData) && empty($postFiles)) {
			return $this->fail('Please define the data to be recorded');
		}

		$postData['serieId'] = $serieId;

		// Se valida los datos de la petición
		$validateRecord = $this->model->validateRecord($postData, $postFiles, 'post');
		if ($validateRecord !== true) {
			return $this->respond(['errors' => $validateRecord], 400);
		}

		$season = $this->model->check($postData['serieId'], $postData['number']);
		if ($season) {
			return $this->respond(['error' => 'There one or many season with same serieId and number'], 409);
		}

		$result = $this->model->insertRecord($postData);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		unset($postData['age']);

		return $this->respondCreated($postData);
	}

	public function update($serieId, $number)
	{
		$season = $this->model->get($serieId, $number);

		// Datos de entrada de la petición
		$postData = $this->request->getPost();
		unset($postData['_method']);
		$postFiles = $this->request->getFiles();

		// Se valida si no existen datos enviados por método POST
		if (empty($postData) && empty($postFiles)) {
			return $this->fail('Please define the data to be recorded');
		}

		// Se obtiene el tipo de petición que se realiza a la función (PUT o PATCH)
		$request = service('request');
		$method = $request->getMethod();

		// Se valida los datos de la petición
		$validateRecord = $this->model->validateRecord($postData, $postFiles, $method, $season);
		if ($validateRecord !== true) {
			return $this->respond(['errors' => $validateRecord], 400);
		}

		if ($postData['serieId'] != $serieId || $postData['number'] != $number) {
			$season = $this->model->check($postData['serieId'], $postData['number']);
			if ($season) {
				return $this->respond(['error' => 'There one or many season with same serieId and number'], 409);
			}
		}

		$result = $this->model->updateRecord($postData, $serieId, $number);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		return $this->success("Record successfully updated");
	}

	public function delete($serieId, $number)
	{
		$result = $this->model->deleteRecord($serieId, $number);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		return $this->success("Record successfully deleted");
	}
}
