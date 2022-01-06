<?php

namespace App\Controllers;

use App\Models\CastingModel;
use App\Models\ChapterModel;
use App\Models\SeasonMegazordModel;
use App\Models\SeasonZordModel;
use App\Models\SerieModel;

class Season extends APIController
{
	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\SeasonModel';

	// Atributos de la clase APIController
	protected $existsRecordMessage = 'The season number is used by other record in this serie';
	protected $parentRecordNotFoundMessage = 'Serie id not found';

	protected function validateDeleteRecord($serieId, $seasonNumber)
	{
		$errors = [];

		// Se valida los registros de Casting,Capítulos,Zords,Megazords asociados a la temporada
		$model = new CastingModel();
		if ($model->checkRecordsByForeignKey(['serieId' => $serieId, 'seasonNumber' => $seasonNumber])) {
			$errors['casting'] = "The season has one or many casting records";
		}
		$model = new ChapterModel();
		if ($model->checkRecordsByForeignKey(['serieId' => $serieId, 'seasonNumber' => $seasonNumber])) {
			$errors['casting'] = "The season has one or many chapters records";
		}
		$model = new SeasonZordModel();
		if ($model->checkRecordsByForeignKey(['serieId' => $serieId, 'seasonNumber' => $seasonNumber])) {
			$errors['zords'] = "The season has one or many zords records";
		}
		$model = new SeasonMegazordModel();
		if ($model->checkRecordsByForeignKey(['serieId' => $serieId, 'seasonNumber' => $seasonNumber])) {
			$errors['megazords'] = "The season has one or many megazords records";
		}

		return count($errors) ? $errors : TRUE;
	}

	protected function checkParentRecord($ids, $isUpdate = FALSE)
	{
		// Se valida los datos de la serie
		$serieModel = new SerieModel();
		$serie = $serieModel->getRecord($isUpdate ? array_slice($ids, 0, 1) : $ids);
		return (bool) $serie !== FALSE;
	}
}
