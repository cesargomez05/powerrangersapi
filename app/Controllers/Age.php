<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Age extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\AgeModel';

	/**
	 * @var \App\Models\AgeModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function index()
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		$ages = $this->model->list($filter);
		return $this->respond($ages);
	}

	public function show($id)
	{
		$validationId = $this->model->validateId($id);
		if ($validationId !== true) {
			return $this->respond(['errors' => $validationId], 400);
		}

		$age = $this->model->get($id);
		if (!isset($age)) {
			return $this->failNotFound('Record not found');
		}
		return $this->respond(['record' => $age]);
	}

	public function create()
	{
		// Datos de entrada de la petición
		$postData = $this->request->getPost();
		$postFiles = $this->request->getFiles();

		// Se valida si no existen datos enviados por método POST
		if (empty($postData) && empty($postFiles)) {
			return $this->fail('Please define the data to be recorded');
		}

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

		// Se "mueve" el archivo subido a la respectiva carpeta
		move_files($postData);

		return $this->respondCreated($postData);
	}

	public function update($id)
	{
		$validationId = $this->model->validateId($id);
		if ($validationId !== true) {
			return $this->respond(['errors' => $validationId], 400);
		}

		$actor = $this->model->get($id);
		if (!isset($actor)) {
			return $this->failNotFound('Record not found');
		}

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
		$validateRecord = $this->model->validateRecord($postData, $postFiles, $method, $actor);
		if ($validateRecord !== true) {
			return $this->respond(['errors' => $validateRecord], 400);
		}

		$result = $this->model->updateRecord($postData, $id);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		// Se "mueve" el archivo subido a la respectiva carpeta
		move_files($postData);

		return $this->success("Record successfully updated");
	}

	public function delete($id)
	{
		$validationId = $this->model->validateId($id);
		if ($validationId !== true) {
			return $this->respond(['errors' => $validationId], 400);
		}

		$actor = $this->model->get($id);
		if (!isset($actor)) {
			return $this->failNotFound('Record not found');
		}

		$result = $this->model->deleteRecord($id);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		return $this->success("Record successfully deleted");
	}

	/*
	protected function validateDeleteRecord($id)
	{
		$errors = [];

		// Se valida los registros de Temporadas asociados a la era
		$model = new SeasonModel();
		if ($model->checkRecordsByForeignKey(['ageId' => $id])) {
			$errors['season'] = "The age has one or many seasons records";
		}

		return count($errors) ? $errors : true;
	}

	protected function addRecordInformation(&$response, $ageUri)
	{
		// Se obtiene la lista de las temporadas asociadas a la era
		$seasonModel = new SeasonModel();
		$response['seasons'] = $seasonModel->getSeasonsByAge($ageUri);
	}*/
}
