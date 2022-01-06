<?php

namespace App\Controllers;

use App\Models\SeasonModel;

class SeasonVillain extends APIController
{
	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\SeasonVillainModel';

	// Atributos de la clase APIController
	protected $existsRecordMessage = 'The record information is used by other record in this season';
	protected $parentRecordNotFoundMessage = 'Season id not found';

	protected function checkParentRecord($ids, $isUpdate = FALSE)
	{
		// Se valida los datos de la temporada
		$seasonModel = new SeasonModel();
		$season = $seasonModel->getRecord($isUpdate ? array_slice($ids, 0, 2) : $ids);
		return (bool) $season !== FALSE;
	}
}
