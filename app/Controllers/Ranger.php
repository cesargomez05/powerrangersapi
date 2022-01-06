<?php

namespace App\Controllers;

use App\Models\CastingModel;
use App\Models\TransformationRangerModel;

class Ranger extends APIController
{
	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\RangerModel';

	protected function validateDeleteRecord($id)
	{
		$errors = [];

		// Se valida los registros de Casting y de transformationRanger asociados al ranger
		$model = new CastingModel();
		if ($model->checkRecordsByForeignKey(['rangerId' => $id])) {
			$errors['casting'] = "The ranger has one or many casting records";
		}
		$model = new TransformationRangerModel();
		if ($model->checkRecordsByForeignKey(['rangerId' => $id])) {
			$errors['transformationRanger'] = 'The ranger has one or many transformation-ranger relation records';
		}

		return count($errors) ? $errors : TRUE;
	}

	protected function moveRecordFiles($filesData, $ranger)
	{
		// Se procede a mover el archivo subido a la carpeta destinada para ello (si aplica)
		if (isset($filesData['record'])) {
			$this->moveFiles($filesData['record'], $ranger);
		}
		if (isset($ranger['morpher']) && isset($filesData['morpher'])) {
			$this->moveFiles($filesData['morpher'], $ranger['morpher']);
		}
	}

	protected function addRecordInformation(&$response, $rangerUri)
	{
		// Se obtiene la información del casting asociado al actor
		$castingModel = new CastingModel();
		$response['casting'] = $castingModel->getCastingByRanger($rangerUri);
	}
}
