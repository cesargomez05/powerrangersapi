<?php

namespace App\Controllers;

use App\Models\SeasonModel;

class Age extends APIController
{
	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\AgeModel';

	protected function validateDeleteRecord($id)
	{
		$errors = [];

		// Se valida los registros de Temporadas asociados a la era
		$model = new SeasonModel();
		if ($model->checkRecordsByForeignKey(['ageId' => $id])) {
			$errors['season'] = "The age has one or many seasons records";
		}

		return count($errors) ? $errors : TRUE;
	}

	protected function addRecordInformation(&$response, $ageUri)
	{
		// Se obtiene la lista de las temporadas asociadas a la era
		$seasonModel = new SeasonModel();
		$response['seasons'] = $seasonModel->getSeasonsByAge($ageUri);
	}
}
