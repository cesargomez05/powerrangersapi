<?php

namespace App\Controllers;

use App\Traits\ControllerTrait;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Season extends BaseResource
{
	use ResponseTrait, ControllerTrait;

	protected $modelName = 'App\Models\SeasonModel';

	/**
	 * @var \App\Models\SeasonModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function create($serieId)
	{
		// Datos de entrada de la petición
		$checkRequestData = $this->checkRequestData($postData, $postFiles);
		if (isset($checkRequestData)) {
			return $checkRequestData;
		}
		$this->setSeasonProperties($postData, $serieId);

		// Se valida los datos de la petición
		$validateRecord = $this->model->validateRecord($postData, $postFiles, 'post');
		if ($validateRecord !== true) {
			return $this->getResponse(400, $validateRecord);
		}

		$checkRecord = $this->checkSeason($postData['serieId'], $postData['number']);
		if (isset($checkRecord)) {
			return $checkRecord;
		}

		$result = $this->model->insertRecord($postData);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->getResponse(500, $result);
		}

		unset($postData['age']);

		return $this->respondCreated($postData);
	}

	public function update($serieId, $number)
	{
		// Datos de entrada de la petición
		$checkRequestData = $this->checkRequestData($postData, $postFiles, $method);
		if (isset($checkRequestData)) {
			return $checkRequestData;
		}

		// Se valida los datos de la petición
		$validateRecord = $this->model->validateRecord(
			$postData,
			$postFiles,
			$method,
			$this->model->get($serieId, $number)
		);
		if ($validateRecord !== true) {
			return $this->getResponse(400, $validateRecord);
		}

		if ($postData['serieId'] != $serieId || $postData['number'] != $number) {
			$checkRecord = $this->checkSeason($postData['serieId'], $postData['number']);
			if (isset($checkRecord)) {
				return $checkRecord;
			}
		}

		$result = $this->model->updateRecord($postData, $serieId, $number);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->getResponse(500, $result);
		}

		return $this->success("Record successfully updated");
	}

	public function listByAge($ageSlug)
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		$records = $this->model->listByAge($filter, $ageSlug);
		return $this->respond($records);
	}

	private function checkSeason($serieId, $number)
	{
		$season = $this->model->check($serieId, $number);
		if ($season) {
			return $this->getResponse(409, 'There one or many season with same serieId and number');
		}
	}
}
