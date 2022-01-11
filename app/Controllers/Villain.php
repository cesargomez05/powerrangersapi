<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Villain extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\VillainModel';

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
		$villain = $this->model->insertRecord($postData);
		if (isset($villain['error'])) {
			// Se retorna los mensajes de error de la validación
			$this->model->db->transRollback();
			return $villain['error'];
		}

		// Se define el Id del villano en el registro de SeasonVillain
		$postData['seasonvillain']['villainId'] = $villain['primaryKey'];

		// Se inserta los datos del SeasonVillain
		$seasonVillainModel = new SeasonVillainModel();
		$seasonVillain = $seasonVillainModel->insertRecord($postData['seasonvillain']);
		if (isset($seasonVillain['error'])) {
			// Se retorna los mensajes de error de la validación
			$this->model->db->transRollback();
			return ['seasonvillain' => $seasonVillain['error']];
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
		$model = new SeasonVillainModel();
		if ($model->checkRecordsByForeignKey(['villainId' => $id])) {
			$errors['seasonVillain'] = "The villain has one or many season-villain relation records";
		}

		return count($errors) ? $errors : true;
	}*/
}
