<?php

namespace App\Controllers;

use App\Models\SeasonModel;

class Serie extends APIController
{
	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\SerieModel';

	protected function insertRecord(&$postData, $filesData)
	{
		// Se inicializa una transacción sobre la base de datos
		$this->model->db->transBegin();

		// Se procede a insertar el registro en la base de datos
		$serie = $this->model->insertRecord($postData);
		if (isset($serie['error'])) {
			// Se retorna los mensajes de error de la validación
			$this->model->db->transRollback();
			return $serie['error'];
		}

		// Se establece los valores correpondientes al Id de la temporada
		$postData['season']['serieId'] = $serie['primaryKey'];
		$postData['season']['number'] = 1;

		// Se inserta los datos de la temporada
		$seasonModel = new SeasonModel();
		$season = $seasonModel->insertRecord($postData['season']);
		if (isset($season['error'])) {
			// Se retorna los mensajes de error de la validación
			$this->model->db->transRollback();
			return ['season' => $season['error']];
		}

		// Se finaliza la transacción
		$this->model->db->transCommit();

		// Se retorna TRUE para indicar que la función se ejecutó correctamente
		return TRUE;
	}

	protected function validateDeleteRecord($id)
	{
		$errors = [];

		// Se valida los registros de Temporadas asociados a la serie
		$model = new SeasonModel();
		if ($model->checkRecordsByForeignKey(['serieId' => $id])) {
			$errors['season'] = "The serie has one or many seasons records";
		}

		return count($errors) ? $errors : TRUE;
	}

	protected function addRecordInformation(&$response, $serieUri)
	{
		// Se obtiene la lista de las temporadas asociadas a la era
		$seasonModel = new SeasonModel();
		$response['seasons'] = $seasonModel->getSeasonsBySerie($serieUri);
	}
}
