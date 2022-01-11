<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Casting extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\CastingModel';

	public function index($serieId, $seasonNumber)
	{
	}

	public function create($serieId, $seasonNumber)
	{
	}

	public function delete($serieId, $seasonNumber, $actorId, $characterId, $rangerId = null)
	{
	}

	/*
	// Atributos de la clase APIController
	protected $existsRecordMessage = 'The casting information is used by other record in this season';
	protected $parentRecordNotFoundMessage = 'Season id not found';

	public function update($serieId = null, $seasonNumber = null, $actorId = null, $characterId = null, $rangerId = null)
	{
		return parent::update($serieId, $seasonNumber, $actorId, $characterId, $rangerId);
	}

	public function delete($serieId = null, $seasonNumber = null, $actorId = null, $characterId = null, $rangerId = null)
	{
		return parent::delete($serieId, $seasonNumber, $actorId, $characterId, $rangerId);
	}

	public function getList($serieSlug = null, $seasonNumber = null, $isTeamUp = null)
	{
		// Se obtiene los parámetros de consulta de registros
		$filter = $this->request->getGet();
		set_pagination($filter);

		// Se ejecuta la consulta de registros
		$records = $this->model->getList($filter, [$serieSlug, $seasonNumber], $isTeamUp);
		return $this->respond($records);
	}

	protected function checkParentRecord($ids, $isUpdate = false)
	{
		// Se valida los datos de la temporada
		$seasonModel = new SeasonModel();
		$season = $seasonModel->getRecord($isUpdate ? array_slice($ids, 0, 2) : $ids);
		return (bool) $season !== false;
	}*/
}
