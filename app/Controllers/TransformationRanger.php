<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class TransformationRanger extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\TransformationRangerModel';

	/**
	 * @var \App\Models\TransformationRangerModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function index($transformationId)
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		$transformationRangers = $this->model->list($filter, $transformationId);
		return $this->respond($transformationRangers);
	}

	public function show($transformationId, $rangerId)
	{
		$transformationRanger = $this->model->get($transformationId, $rangerId);
		return $this->respond(['record' => $transformationRanger]);
	}

	public function create($transformationId)
	{
		// Datos de entrada de la petición
		$postData = $this->request->getPost();
		$postFiles = $this->request->getFiles();

		// Se valida si no existen datos enviados por método POST
		if (empty($postData) && empty($postFiles)) {
			return $this->fail('Please define the data to be recorded');
		}

		$postData['transformationId'] = $transformationId;

		// Se valida los datos de la petición
		$validateRecord = $this->model->validateRecord($postData, $postFiles, 'post');
		if ($validateRecord !== true) {
			return $this->respond(['errors' => $validateRecord], 400);
		}

		$transformationRanger = $this->model->check($postData['transformationId'], $postData['rangerId']);
		if ($transformationRanger) {
			return $this->respond(['error' => 'There one or many transformationRanger with same transformationId and rangerId'], 409);
		}

		$result = $this->model->insertRecord($postData);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		return $this->respondCreated($postData);
	}

	public function update($transformationId, $rangerId)
	{
		$transformationRanger = $this->model->get($transformationId, $rangerId);

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
		$validateRecord = $this->model->validateRecord($postData, $postFiles, $method, $transformationRanger);
		if ($validateRecord !== true) {
			return $this->respond(['errors' => $validateRecord], 400);
		}

		if ($postData['transformationId'] != $transformationId || $postData['rangerId'] != $rangerId) {
			$transformationRanger = $this->model->check($postData['transformationId'], $postData['rangerId']);
			if ($transformationRanger) {
				return $this->respond(['error' => 'There one or many transformationRanger with same transformationId and rangerId'], 409);
			}
		}

		$result = $this->model->updateRecord($postData, $transformationId, $rangerId);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		return $this->success("Record successfully updated");
	}

	public function delete($transformationId, $rangerId)
	{
		$result = $this->model->deleteRecord($transformationId, $rangerId);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		return $this->success("Record successfully deleted");
	}
}
