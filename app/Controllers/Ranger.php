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
		$ranger = $this->model->get($id)->toArray();

		// Datos de entrada de la petición
		$postData = $this->request->getPost();
		unset($postData['_method']);
		$postFiles = $this->request->getFiles();

		// Se valida si no existen datos enviados por método POST
		if (empty($postData) && empty($postFiles)) {
			return $this->fail('Please define the data to be recorded');
		}

		// Se elimina las propiedades asociadas al morpher en la actualización de datos del ranger
		unset($postData['morpherId']);
		unset($postData['morpher']);
		unset($postFiles['morpher']);

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
}
