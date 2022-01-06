<?php

namespace App\Controllers;

use App\Models\SeasonArsenalModel;

class Arsenal extends APIController
{
	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\ArsenalModel';

	protected function insertRecord(&$postData, $filesData)
	{
		// Se inicializa una transacción sobre la base de datos
		$this->model->db->transBegin();

		// Se procede a insertar el registro en la base de datos
		$arsenal = $this->model->insertRecord($postData);
		if (isset($arsenal['error'])) {
			// Se retorna los mensajes de error de la validación
			$this->model->db->transRollback();
			return $arsenal['error'];
		}

		// Se define el Id del arsenal en el registro de SeasonArsenal
		$postData['seasonarsenal']['arsenalId'] = $arsenal['primaryKey'];

		// Se inserta los datos del SeasonArsenal
		$seasonArsenalModel = new SeasonArsenalModel();
		$seasonArsenal = $seasonArsenalModel->insertRecord($postData['seasonarsenal']);
		if (isset($seasonArsenal['error'])) {
			// Se retorna los mensajes de error de la validación
			$this->model->db->transRollback();
			return ['seasonarsenal' => $seasonArsenal['error']];
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
		$model = new SeasonArsenalModel();
		if ($model->checkRecordsByForeignKey(['arsenalId' => $id])) {
			$errors['seasonArsenal'] = "The arsenal has one or many relation season-arsenal records";
		}

		return count($errors) ? $errors : TRUE;
	}
}
