<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Megazord extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\MegazordModel';

	/**
	 * @var \App\Models\MegazordModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function index()
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		$megazords = $this->model->list($filter);
		return $this->respond($megazords);
	}

	public function show($id)
	{
		$megazord = $this->model->get($id);
		return $this->respond(['record' => $megazord]);
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

		unset($postData['seasonmegazord']);
		unset($postData['zordsId']);

		return $this->respondCreated($postData);
	}

	public function update($id)
	{
		$megazord = $this->model->get($id);

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
		$validateRecord = $this->model->validateRecord($postData, $postFiles, $method, $megazord);
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
		// Se consulta si existe registros dependientes del registro a eliminar
		$validations = $this->model->validateNestedRecords($id);
		if (count($validations) > 0) {
			return $this->respond(['errors' => $validations], 409);
		}

		$result = $this->model->deleteRecord($id);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		return $this->success("Record successfully deleted");
	}
}
