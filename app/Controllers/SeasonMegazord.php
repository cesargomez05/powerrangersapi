<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class SeasonMegazord extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\SeasonMegazordModel';

	public function index($serieId, $seasonNumber)
	{
	}

	public function create($serieId, $seasonNumber)
	{
	}

	public function delete($serieId, $seasonNumber, $megazordId)
	{
	}

	/*
	// Atributos de la clase APIController
	protected $existsRecordMessage = 'The record information is used by other record in this season';
	protected $parentRecordNotFoundMessage = 'Season id not found';

	protected function checkParentRecord($ids, $isUpdate = false)
	{
		// Se valida los datos de la temporada
		$seasonModel = new SeasonModel();
		$season = $seasonModel->getRecord($isUpdate ? array_slice($ids, 0, 2) : $ids);
		return (bool) $season !== false;
	}*/
}
