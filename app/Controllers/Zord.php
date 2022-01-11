<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Zord extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\ZordModel';

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
		$zord = $this->model->insertRecord($postData);
		if (isset($zord['error'])) {
			// Se retorna los mensajes de error de la validación
			$this->model->db->transRollback();
			return $zord['error'];
		}

		// Se define el Id del zord en el registro de SeasonZord
		$postData['seasonzord']['zordId'] = $zord['primaryKey'];

		// Se inserta los datos del SeasonZord
		$seasonZordModel = new SeasonZordModel();
		$seasonZord = $seasonZordModel->insertRecord($postData['seasonzord']);
		if (isset($seasonZord['error'])) {
			// Se retorna los mensajes de error de la validación
			$this->model->db->transRollback();
			return ['seasonzord' => $seasonZord['error']];
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
		$model = new SeasonZordModel();
		if ($model->checkRecordsByForeignKey(['zordId' => $id])) {
			$errors['seasonZord'] = "The zord has one or many relation season-zord records";
		}
		$model = new MegazordZordModel();
		if ($model->checkRecordsByForeignKey(['zordId' => $id])) {
			$errors['megazordZord'] = "The zord has one or many relation megazord-zord records";
		}

		return count($errors) ? $errors : true;
	}*/
}
