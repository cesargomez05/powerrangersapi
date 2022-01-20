<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class SerieModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
	}

	protected $table = 'series';

	protected $allowedFields = ['slug', 'title'];

	protected $nestedModelClasses = [
		'seasonModel' => 'App\Models\SeasonModel'
	];

	protected $validationRules = [
		'slug' => 'required_with[title]|max_length[50]|is_unique[series.slug,id,{_id}]',
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

	protected function setRecordCondition($id)
	{
		$this->where('id', $id);
	}

	protected function checkNestedRecords($id, &$errors)
	{
		$seasonModel = model($this->nestedModelClasses['seasonModel']);
		if ($seasonModel->countBySerieId($id)) {
			$errors['season'] = 'There are nested season records to serie';
		}
	}

	public function insertRecord(&$record)
	{
		$this->db->transBegin();

		// Se procede a insertar el registro en la base de datos
		$result = $this->insertRecordTrait($record);
		if ($result !== true) {
			$this->db->transRollback();
			return $result;
		}

		// Se establece el Id de la serie creada en los datos de la temporada
		$record['season']['serieId'] = $record[$this->primaryKey];

		$seasonModel = model($this->nestedModelClasses['seasonModel']);
		$seasonResult = $seasonModel->insertRecord($record['season'], true);
		if ($seasonResult !== true) {
			$this->db->transRollback();
			return $seasonResult;
		}

		$this->db->transCommit();

		return true;
	}

	public function validateRecord(&$postData, $postFiles, $method, $prevRecord = null)
	{
		$errors = [];

		$this->validateRecordProperties($postData, $method, $prevRecord);

		// Se valida los datos de la temporada (si es método POST)
		if ($method == 'post') {
			if (isset($postData['season'])) {
				$seasonModel = model($this->nestedModelClasses['seasonModel']);

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

		return empty($errors) ? true : $errors;
	}
}
