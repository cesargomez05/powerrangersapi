<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Ranger extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\RangerModel';

	/**
	 * @var \App\Models\RangerModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function index()
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		$rangers = $this->model->list($filter);
		return $this->respond($rangers);
	}

	public function show($id)
	{
		$validationId = $this->model->validateId($id);
		if ($validationId !== true) {
			return $this->respond(['errors' => $validationId], 400);
		}

		$ranger = $this->model->get($id);
		if (!isset($ranger)) {
			return $this->failNotFound('Record not found');
		}
		return $this->respond(['record' => $ranger]);
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

		// Se "mueve" los archivos subidos a la respectiva carpeta
		move_files($postData);
		move_files($postData['morpher']);
		unset($postData['morpher']);

		return $this->respondCreated($postData);
	}

	public function update($id)
	{
		$validationId = $this->model->validateId($id);
		if ($validationId !== true) {
			return $this->respond(['errors' => $validationId], 400);
		}

		$ranger = $this->model->get($id);
		if (!isset($ranger)) {
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
		$validateRecord = $this->model->validateRecord($postData, $postFiles, $method, $ranger);
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
		move_files($postData['morpher']);

		return $this->success("Record successfully updated");
	}

	public function delete($id)
	{
		$validationId = $this->model->validateId($id);
		if ($validationId !== true) {
			return $this->respond(['errors' => $validationId], 400);
		}

		$ranger = $this->model->get($id);
		if (!isset($ranger)) {
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

		// Se valida los registros de Casting y de transformationRanger asociados al ranger
		$model = new CastingModel();
		if ($model->checkRecordsByForeignKey(['rangerId' => $id])) {
			$errors['casting'] = "The ranger has one or many casting records";
		}
		$model = new TransformationRangerModel();
		if ($model->checkRecordsByForeignKey(['rangerId' => $id])) {
			$errors['transformationRanger'] = 'The ranger has one or many transformation-ranger relation records';
		}

		return count($errors) ? $errors : true;
	}

	protected function moveRecordFiles($filesData, $ranger)
	{
		// Se procede a mover el archivo subido a la carpeta destinada para ello (si aplica)
		if (isset($filesData['record'])) {
			$this->moveFiles($filesData['record'], $ranger);
		}
		if (isset($ranger['morpher']) && isset($filesData['morpher'])) {
			$this->moveFiles($filesData['morpher'], $ranger['morpher']);
		}
	}

	protected function addRecordInformation(&$response, $rangerUri)
	{
		// Se obtiene la información del casting asociado al actor
		$castingModel = new CastingModel();
		$response['casting'] = $castingModel->getCastingByRanger($rangerUri);
	}*/
}
