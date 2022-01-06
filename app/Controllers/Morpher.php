<?php

namespace App\Controllers;

use App\Models\RangerModel;

class Morpher extends APIController
{
	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\MorpherModel';

	protected function insertRecord(&$postData, $filesData)
	{
		// Se inicializa una transacción sobre la base de datos
		$this->model->db->transBegin();

		// Se procede a insertar el registro en la base de datos
		$morpher = $this->model->insertRecord($postData);
		if (isset($morpher['error'])) {
			// Se retorna los mensajes de error de la validación
			$this->model->db->transRollback();
			return $morpher['error'];
		}

		// Se procede a establecer el Id del morpher creado a los respectivos rangers (si aplica)
		if (isset($postData['rangersId'])) {
			$rangerModel = new RangerModel();
			$rangers = $rangerModel->setRangersMorpher($postData['rangersId'], $morpher['primaryKey']);
			if (isset($rangers['error'])) {
				// Se retorna los mensajes de error de la validación
				$this->model->db->transRollback();
				return $rangers['error'];
			}
		}

		// Se finaliza la transacción
		$this->model->db->transCommit();

		// Se procede a mover los archivos asociados al morpher
		$this->moveRecordFiles($filesData, $postData);

		// Se retorna TRUE para indicar que la función se ejecutó correctamente
		return TRUE;
	}

	protected function validateDeleteRecord($id)
	{
		$errors = [];

		// Se valida los registros de Casting y de transformationRanger asociados al ranger
		$model = new RangerModel();
		if ($model->checkRecordsByForeignKey(['morpherId' => $id])) {
			$errors['ranger'] = "The morpher has one or many rangers records";
		}

		return count($errors) ? $errors : TRUE;
	}
}
