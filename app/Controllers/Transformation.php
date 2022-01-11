<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Transformation extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\TransformationModel';

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
		$transformation = $this->model->insertRecord($postData);
		if (isset($transformation['error'])) {
			// Se retorna los mensajes de error de la validación
			$this->model->db->transRollback();
			return $transformation['error'];
		}

		// Se recorre la lista de rangers, para establecer el Id de la transformación
		foreach ($postData['rangers'] as $key => &$ranger) {
			$ranger['transformationId'] = $transformation['primaryKey'];
		}

		// Se inserta el lote de datos de los ranges asociados a la transformación
		$transformationRangerModel = new TransformationRangerModel();
		$result = $transformationRangerModel->insertBatch($postData['rangers']);
		if ($result === false) {
			$this->db->transRollback();
			return ['rangers' => $transformationRangerModel->errors()];
		}

		// Se finaliza la transacción
		$this->model->db->transCommit();

		// Se procede a mover los archivos asociados al registro
		$this->moveRecordFiles($filesData, $postData);

		// Se retorna true para indicar que la función se ejecutó correctamente
		return true;
	}

	protected function moveRecordFiles($filesData, $transformation)
	{
		if (isset($filesData['record'])) {
			$this->moveFiles($filesData['record'], $transformation);
		}
		if (isset($transformation['rangers'])) {
			foreach ($transformation['rangers'] as $key => $ranger) {
				if (isset($filesData['rangers'][$key])) {
					$this->moveFiles($filesData['rangers'][$key], $ranger);
				}
			}
		}
	}

	protected function validateDeleteRecord($id)
	{
		$errors = [];

		// Se consulta los registros de transformationRanger asociados a la transformación
		$model = new TransformationRangerModel();
		if ($model->checkRecordsByForeignKey(['transformationId' => $id])) {
			$errors['transformationRanger'] = 'The transformation has one or many relation transformation-ranger records';
		}

		return count($errors) ? $errors : true;
	}*/
}
