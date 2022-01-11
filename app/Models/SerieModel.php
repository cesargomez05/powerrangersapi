<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class SerieModel extends Model
{
	use ModelTrait;

	protected $table = 'series';

	protected $allowedFields = ['slug', 'title'];

	protected $validationRules = [
		'slug' => 'required|max_length[50]|is_unique[series.slug,id,{_id}]',
		'title' => 'required|max_length[50]'
	];

	protected function setRecordsCondition($query)
	{
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('title', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	public function get($id)
	{
		$this->where('id', $id);
		$record = $this->findAll();
		return count($record) ? $record[0] : null;
	}

	public function insertRecord(&$record)
	{
		$this->db->transBegin();

		// Se procede a insertar el registro en la base de datos
		$recordId = $this->insert($record);
		if ($recordId === false) {
			$this->db->transRollback();
			return $this->errors();
		}

		if ($recordId !== 0) {
			$record[$this->primaryKey] = $recordId;
		}

		// Se establece el Id de la serie creada en los datos de la temporada
		$record['season']['serieId'] = $recordId;

		$seasonModel = new \App\Models\SeasonModel();
		$seasonResult = $seasonModel->insertRecord($record['season'], true);
		if ($seasonResult !== true) {
			$this->db->transRollback();
			return $seasonResult;
		}

		$this->db->transCommit();

		return true;
	}

	public function updateRecord($record, $id)
	{
		$this->where('id', $id);

		$result = $this->update(null, $record);
		return $result === false ? $this->errors() : true;
	}

	public function deleteRecord($id)
	{
		$this->where('id', $id);
		if (!$this->delete()) {
			return $this->errors();
		}
		return true;
	}

	public function validateRecord(&$postData, $postFiles, $method, $prevRecord = null)
	{
		$errors = [];

		$this->validateRecordProperties($postData, $method, $prevRecord);

		// Se valida los datos de la temporada (si es método POST)
		if ($method == 'post') {
			if (isset($postData['season'])) {
				$seasonModel = new \App\Models\SeasonModel();

				// Se omite la validación del id de la temporada asociada a la serie a crear
				$seasonModel->setValidationRule('serieId', 'permit_empty');
				unset($postData['season']['serieId']);
				// Se define el número de temporada asociada a la serie a crear
				$postData['season']['number'] = 1;

				$seasonErrors = $seasonModel->validateRecord($postData['season'], isset($postFiles['season']) ? $postFiles['season'] : [], 'post');
				if ($seasonErrors !== true) {
					$errors['season'] = $seasonErrors;
				}
			} else {
				$errors['season'] = 'The season information is required';
			}
		} else {
			unset($postData['season']);
		}

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return count($errors) > 0 ? $errors : true;
	}
}
