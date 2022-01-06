<?php

namespace App\Controllers;

use App\Models\CastingModel;

class Character extends APIController
{
	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\CharacterModel';

	protected function validateDeleteRecord($id)
	{
		$errors = [];

		// Se valida los registros de Casting asociados al personaje
		$model = new CastingModel();
		if ($model->checkRecordsByForeignKey(['characterId' => $id])) {
			$errors['casting'] = "The character has one or many casting records";
		}

		return count($errors) ? $errors : TRUE;
	}

	protected function addRecordInformation(&$response, $characterUri)
	{
		// Se obtiene la información del casting asociado al actor
		$castingModel = new CastingModel();
		$response['casting'] = $castingModel->getCastingByCharacter($characterUri);
	}
}
