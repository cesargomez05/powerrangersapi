<?php

namespace App\Controllers;

use App\Models\MegazordZordModel;
use App\Models\SeasonMegazordModel;

class Megazord extends APIController
{
	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\MegazordModel';

	protected function insertRecord(&$postData, $filesData)
	{
		// Se inicializa una transacción sobre la base de datos
		$this->model->db->transBegin();

		// Se procede a insertar el registro en la base de datos
		$megazord = $this->model->insertRecord($postData);
		if (isset($megazord['error'])) {
			// Se retorna los mensajes de error de la validación
			$this->model->db->transRollback();
			return $megazord['error'];
		}

		// Se define el Id del megazord en el registro de SeasonMegazord
		$postData['seasonmegazord']['megazordId'] = $megazord['primaryKey'];

		// Se inserta los datos del SeasonMegazord
		$seasonMegazordModel = new SeasonMegazordModel();
		$seasonMegazord = $seasonMegazordModel->insertRecord($postData['seasonmegazord']);
		if (isset($seasonMegazord['error'])) {
			// Se retorna los mensajes de error de la validación
			$this->model->db->transRollback();
			return ['seasonmegazord' => $seasonMegazord['error']];
		}

		// Se genera el conjunto de registros de la relación entre el Megazord y los Zords que lo conforman (si aplica)
		if (isset($postData['zordsId']) && strlen($postData['zordsId'])) {
			$zordsId = [];
			foreach (explode(',', $postData['zordsId']) as $zordId) {
				array_push($zordsId, ['megazordId' => $megazord['primaryKey'], 'zordId' => $zordId]);
			}

			$megazordZordModel = new MegazordZordModel();
			$megazordZordModel->removeValidationRule('megazordId');
			$result = $megazordZordModel->insertBatch($zordsId);
			if ($result === FALSE) {
				$this->model->db->transRollback();
				return ['zordsId' => $megazordZordModel->errors()];
			}
		}

		// Se finaliza la transacción
		$this->model->db->transCommit();

		// Se procede a mover los archivos asociados al arsenal
		$this->moveRecordFiles($filesData, $postData);

		// Se retorna TRUE para indicar que la función se ejecutó correctamente
		return TRUE;
	}

	protected function validateDeleteRecord($id)
	{
		$errors = [];

		// Se consulta los registros 
		$model = new SeasonMegazordModel();
		if ($model->checkRecordsByForeignKey(['megazordId' => $id])) {
			$errors['seasonMegazord'] = "The megazord has one or many season-megazord relation records";
		}
		$model = new MegazordZordModel();
		if ($model->checkRecordsByForeignKey(['megazordId' => $id])) {
			$errors['megazordZord'] = "The megazord has one or many megazord-zord relation records";
		}

		return count($errors) ? $errors : TRUE;
	}
}
