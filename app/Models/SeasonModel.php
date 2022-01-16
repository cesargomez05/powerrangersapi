<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class SeasonModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
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

	protected function setRecordsCondition($query, $serieId)
	{
		$this->where('serieId', $serieId);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('title', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($serieId, $number)
	{
		$this->where('serieId', $serieId)->where('number', $number);
	}

	public function insertRecord(&$record, $subTransaction = false)
	{
		if (!$subTransaction) {
			$this->db->transBegin();
		}

		if (isset($record['age'])) {
			$ageModel = model('App\Models\AgeModel');
			$ageResult = $ageModel->insertRecord($record['age']);
			if ($ageResult !== true) {
				$this->db->transRollback();
				return $ageResult;
			}

			$record['ageId'] = $record['age']['id'];
		}

		// Se procede a insertar el registro en la base de datos
		$result = $this->insertRecordTrait($record);
		if ($result !== true) {
			$this->db->transRollback();
			return $result;
		}

		if (!$subTransaction) {
			$this->db->transCommit();
		}

		return true;
	}

	public function validateRecord(&$postData, $postFiles, $method, $prevRecord = null)
	{
		$errors = [];

		$this->validateRecordProperties($postData, $method, $prevRecord);

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		// Se valida los datos de la era
		if (isset($postData['age'])) {
			// Se omite la validación del Id de la era
			$this->setValidationRule('ageId', 'permit_empty');
			unset($postData['ageId']);

			$ageModel = model('App\Models\AgeModel');
			$ageErrors = $ageModel->validateRecord($postData['age'], isset($postFiles['age']) ? $postFiles['age'] : [], 'post');
			if ($ageErrors !== true) {
				$errors['age'] = $ageErrors;
			}
		}

		return count($errors) > 0 ? $errors : true;
	}
}
