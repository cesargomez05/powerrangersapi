<?php

namespace App\Controllers;

use App\Traits\ControllerTrait;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Actor extends BaseResource
{
	use ResponseTrait, ControllerTrait;

	protected $modelName = 'App\Models\ActorModel';

	/**
	 * @var \App\Models\ActorModel
	 */
	protected $model;

	protected $helpers = ['app'];

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
		$actor = $this->model->get($id);

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

	public function indexPublic()
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		$actors = $this->model->listPublic($filter);
		return $this->respond($actors);
	}

	public function showPublic($slug)
	{
		$actor = $this->model->getPublic($slug);
		return $this->respond(['record' => $actor]);
	}
}
