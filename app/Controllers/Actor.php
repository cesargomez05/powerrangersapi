<?php

namespace App\Controllers;

use App\Models\CastingModel;

class Actor extends APIController
{
	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\ActorModel';

	protected function validateDeleteRecord($id)
	{
		$errors = [];

		// Se valida los registros de Casting asociados al actor
		$model = new CastingModel();
		if ($model->checkRecordsByForeignKey(['actorId' => $id])) {
			$errors['casting'] = "The actor has one or many casting records";
		}

		return count($errors) ? $errors : TRUE;
	}

	protected function addRecordInformation(&$response, $actorUri)
	{
		// Se obtiene la información del casting asociado al actor
		$castingModel = new CastingModel();
		$response['casting'] = $castingModel->getCastingByActor($actorUri);
	}
}
