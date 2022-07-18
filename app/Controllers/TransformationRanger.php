<?php

namespace App\Controllers;

use App\Traits\ControllerTrait;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class TransformationRanger extends BaseResource
{
	use ResponseTrait, ControllerTrait;

	protected $modelName = 'App\Models\TransformationRangerModel';

	/**
	 * @var \App\Models\TransformationRangerModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function create($transformationId)
	{
		// Datos de entrada de la petición
		$checkRequestData = $this->checkRequestData($postData, $postFiles);
		if (isset($checkRequestData)) {
			return $checkRequestData;
		}
		$this->addSegmentProperties($postData, ['transformationId' => $transformationId]);

		// Se valida los datos de la petición
		$validateRecord = $this->model->validateRecord($postData, $postFiles, 'post');
		if ($validateRecord !== true) {
			return $this->getResponse(400, $validateRecord);
		}

		$checkRecord = $this->checkTransformationRanger($postData['transformationId'], $postData['rangerId']);
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

	public function update($transformationId, $rangerId)
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
			$this->model->get($transformationId, $rangerId)
		);
		if ($validateRecord !== true) {
			return $this->getResponse(400, $validateRecord);
		}

		if ($postData['transformationId'] != $transformationId || $postData['rangerId'] != $rangerId) {
			$checkRecord = $this->checkTransformationRanger($postData['transformationId'], $postData['rangerId']);
			if (isset($checkRecord)) {
				return $checkRecord;
			}
		}

		$result = $this->model->updateRecord($postData, $transformationId, $rangerId);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->getResponse(500, $result);
		}

		return $this->success("Record successfully updated");
	}

	private function checkTransformationRanger($transformationId, $rangerId)
	{
		$transformationRanger = $this->model->check($transformationId, $rangerId);
		if ($transformationRanger) {
			return $this->getResponse(409, 'There one or many transformationRanger with same transformationId and rangerId');
		}
	}
}
