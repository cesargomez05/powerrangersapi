<?php

namespace App\Controllers;

use App\Traits\ControllerTrait;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Chapter extends BaseResource
{
	use ResponseTrait, ControllerTrait;

	protected $modelName = 'App\Models\ChapterModel';

	/**
	 * @var \App\Models\ChapterModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function create($serieId, $seasonNumber)
	{
		// Datos de entrada de la petición
		$checkRequestData = $this->checkRequestData($postData, $postFiles);
		if (isset($checkRequestData)) {
			return $checkRequestData;
		}
		$this->setSeasonProperties($postData, $serieId, $seasonNumber);

		// Se valida los datos de la petición
		$validateRecord = $this->model->validateRecord($postData, $postFiles, 'post');
		if ($validateRecord !== true) {
			return $this->getResponse(400, $validateRecord);
		}

		$checkRecord = $this->checkChapter($serieId, $seasonNumber, $postData['number']);
		if (isset($checkRecord)) {
			return $checkRecord;
		}

		$result = $this->model->insertRecord($postData);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->getResponse(500, $result);
		}

		return $this->respondCreated($postData);
	}

	public function update($serieId, $seasonNumber, $number)
	{
		// Datos de entrada de la petición
		$checkRequestData = $this->checkRequestData($postData, $postFiles, $method);
		if (isset($checkRequestData)) {
			return $checkRequestData;
		}
		$this->setSeasonProperties($postData, $serieId, $seasonNumber);

		// Se valida los datos de la petición
		$validateRecord = $this->model->validateRecord(
			$postData,
			$postFiles,
			$method,
			$this->model->get($serieId, $seasonNumber, $number)
		);
		if ($validateRecord !== true) {
			return $this->getResponse(400, $validateRecord);
		}

		if ($postData['number'] != $number) {
			$checkRecord = $this->checkChapter($serieId, $seasonNumber, $postData['number']);
			if (isset($checkRecord)) {
				return $checkRecord;
			}
		}

		$result = $this->model->updateRecord($postData, $serieId, $seasonNumber, $number);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->getResponse(500, $result);
		}

		return $this->success("Record successfully updated");
	}

	private function checkChapter($serieId, $seasonNumber, $number)
	{
		$chapter = $this->model->check($serieId, $seasonNumber, $number);
		if (isset($chapter)) {
			return $this->getResponse(409, 'There one or many chapter with same number for the season');
		}
	}
}
