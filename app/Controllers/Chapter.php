<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Chapter extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\ChapterModel';

	/**
	 * @var \App\Models\ChapterModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function index($serieId, $seasonNumber)
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		$chapters = $this->model->list($filter, $serieId, $seasonNumber);
		return $this->respond($chapters);
	}

	public function show($serieId, $seasonNumber, $number)
	{
		$chapter = $this->model->get($serieId, $seasonNumber, $number);
		if (!isset($chapter)) {
			return $this->failNotFound('Record not found');
		}
		return $this->respond(['record' => $chapter]);
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

		$chapter = $this->model->get($serieId, $seasonNumber, $postData['number']);
		if (isset($chapter)) {
			return $this->respond(['error' => 'There one or many chapter with same number for the season'], 409);
		}

		$result = $this->model->insertRecord($postData);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		return $this->respondCreated($postData);
	}

	public function update($serieId, $seasonNumber, $number)
	{
		$chapter = $this->model->get($serieId, $seasonNumber, $number);
		if (!isset($chapter)) {
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

		$postData['serieId'] = $serieId;
		$postData['seasonNumber'] = $seasonNumber;

		// Se obtiene el tipo de petición que se realiza a la función (PUT o PATCH)
		$request = service('request');
		$method = $request->getMethod();

		// Se valida los datos de la petición
		$validateRecord = $this->model->validateRecord($postData, $postFiles, $method, $chapter);
		if ($validateRecord !== true) {
			return $this->respond(['errors' => $validateRecord], 400);
		}

		if ($postData['number'] != $number) {
			$chapter = $this->model->get($serieId, $seasonNumber, $postData['number']);
			if (isset($chapter)) {
				return $this->respond(['error' => 'There one or many chapter with same number for the season'], 409);
			}
		}

		$result = $this->model->updateRecord($postData, $serieId, $seasonNumber, $number);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		return $this->success("Record successfully updated");
	}

	public function delete($serieId, $seasonNumber, $number)
	{
		$chapter = $this->model->get($serieId, $seasonNumber, $number);
		if (!isset($chapter)) {
			return $this->failNotFound('Record not found');
		}

		$result = $this->model->deleteRecord($serieId, $seasonNumber, $number);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		return $this->success("Record successfully deleted");
	}
}
