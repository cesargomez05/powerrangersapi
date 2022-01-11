<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class SeasonModel extends Model
{
	use ModelTrait {
		list as public listTrait;
		validateId as public validateIdTrait;
	}

	protected $table = 'seasons';

	protected $allowedFields = ['serieId', 'number', 'year', 'title', 'ageId', 'synopsis'];

	protected $validationRules = [
		'serieId' => 'required|is_natural_no_zero|exists_id[series.id]',
		'number' => 'required|is_natural_no_zero',
		'year' => 'permit_empty|is_year',
		'title' => 'permit_empty|max_length[50]',
		'synopsis' => 'permit_empty',
		'ageId' => 'required|is_natural_no_zero|exists_id[ages.id]'
	];

	public function validateId($serieId, $seasonNumber)
	{
		$validation = \Config\Services::validation();
		$validation->setRule('seasonNumber', 'Season number is not valid', 'required|is_natural_no_zero');
		if ($validation->run(['serieId' => $serieId, 'seasonNumber' => $seasonNumber])) {
			return true;
		} else {
			return $validation->getErrors();
		}
	}

	public function list($serieId, $query)
	{
		$this->where('serieId', $serieId);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('title', $query['q'], 'both');
			$this->groupEnd();
		}

		return $this->listTrait($query);
	}

	public function get($serieId, $number)
	{
		$this->where('serieId', $serieId)->where('number', $number);
		$record = $this->findAll();
		return count($record) ? $record[0] : null;
	}

	public function insertRecord(&$record, $subTransaction = false)
	{
		if (!$subTransaction) {
			$this->db->transBegin();
		}

		if (isset($record['age'])) {
			$ageModel = new \App\Models\AgeModel();
			$ageResult = $ageModel->insertRecord($record['age']);
			if ($ageResult !== true) {
				$this->db->transRollback();
				return $ageResult;
			}

			$record['ageId'] = $record['age']['id'];
		}

		// Se procede a insertar el registro en la base de datos
		$recordId = $this->insert($record);
		if ($recordId === false) {
			$this->db->transRollback();
			return $this->errors();
		}

		if ($recordId !== 0) {
			$record[$this->primaryKey] = $recordId;
		}

		if (!$subTransaction) {
			$this->db->transCommit();
		}

		return true;
	}

	public function updateRecord($record, $serieId, $number)
	{
		$this->where('serieId', $serieId)->where('number', $number);

		$result = $this->update(null, $record);
		return $result === false ? $this->errors() : true;
	}

	public function deleteRecord($serieId, $number)
	{
		$this->where('serieId', $serieId)->where('number', $number);
		if (!$this->delete()) {
			return $this->errors();
		}
		return true;
	}

	public function validateRecord(&$postData, $postFiles, $method, $prevRecord = null)
	{
		$errors = [];

		$this->validateRecordProperties($postData, $method, $prevRecord);

		// Se valida los datos de la era
		if (isset($postData['age'])) {
			// Se omite la validación del Id de la era
			$this->setValidationRule('ageId', 'permit_empty');
			unset($postData['ageId']);

			$ageModel = new \App\Models\AgeModel();
			$ageErrors = $ageModel->validateRecord($postData['age'], isset($postFiles['age']) ? $postFiles['age'] : [], 'post');
			if ($ageErrors !== true) {
				$errors['age'] = $ageErrors;
			}
		}

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return count($errors) > 0 ? $errors : true;
	}
}
