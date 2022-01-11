<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Arsenal extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\ArsenalModel';

	public function index()
	{
	}

	public function show($id)
	{
	}

	public function create()
	{
	}

	public function update($id)
	{
	}

	public function delete($id)
	{
	}

	/*
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

		// Se retorna true para indicar que la función se ejecutó correctamente
		return true;
	}

	protected function validateDeleteRecord($id)
	{
		$errors = [];

		// Se consulta los registros 
		$model = new SeasonArsenalModel();
		if ($model->checkRecordsByForeignKey(['arsenalId' => $id])) {
			$errors['seasonArsenal'] = "The arsenal has one or many relation season-arsenal records";
		}

		return count($errors) ? $errors : true;
	}*/
}
