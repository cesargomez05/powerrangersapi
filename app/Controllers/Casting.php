<?php

namespace App\Controllers;

use App\Traits\ControllerTrait;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Casting extends BaseResource
{
	use ResponseTrait, ControllerTrait;

	protected $modelName = 'App\Models\CastingModel';

	/**
	 * @var \App\Models\CastingModel
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

		$result = $this->model->insertRecord($postData);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->getResponse(500, $result);
		}

		move_files($postData['actor']);
		move_files($postData['character']);
		move_files($postData['ranger']);
		move_files($postData['ranger']['morpher']);

		return $this->success("Record successfully created", 201);
	}

	public function indexTeamUpPublic($serieSlug, $seasonNumber)
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		$records = $this->model->listTeamUpPublic($filter, $serieSlug, $seasonNumber);
		return $this->respond($records);
	}

	public function listByActor($actorSlug)
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		$records = $this->model->listByActor($filter, $actorSlug);
		return $this->respond($records);
	}

	public function listByCharacter($characterSlug)
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		$records = $this->model->listByCharacter($filter, $characterSlug);
		return $this->respond($records);
	}

	public function listByRanger($rangerSlug)
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		$records = $this->model->listByRanger($filter, $rangerSlug);
		return $this->respond($records);
	}
}
