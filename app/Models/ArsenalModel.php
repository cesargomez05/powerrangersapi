<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class ArsenalModel extends Model
{
	use ModelTrait;

	protected $table = 'arsenal';

	protected $allowedFields = ['slug', 'name', 'description', 'photo'];

	protected $validationRules = [
		'slug' => 'required_with[name]|max_length[100]',
		'name' => 'required|max_length[100]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[25]'
	];

	protected function setRecordsCondition($query)
	{
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('name', $query['q'], 'both');
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

		// Se inserta los datos de la relación Temporada-Arsenal (si aplica)
		if (isset($record['seasonarsenal'])) {
			$record['seasonarsenal']['arsenalId'] = $recordId;

			$seasonArsenalModel = model('App\Models\SeasonArsenalModel');
			$seasonArsenalResult = $seasonArsenalModel->insertRecord($record['seasonarsenal']);
			if ($seasonArsenalResult !== true) {
				$this->db->transRollback();
				return $seasonArsenalResult;
			}
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
		$errors = $this->validateUploadFiles($postData, $postFiles);
		if ($errors === true) {
			$errors = [];
		}

		$this->validateRecordProperties($postData, $method, $prevRecord);

		$slugSettings = ['title' => 'name', 'field' => 'slug', 'id' => [$this->primaryKey]];
		$this->setSlugValue($postData, $slugSettings, isset($prevRecord) ? [$prevRecord[$this->primaryKey]] : null);

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		// Se valida los datos de la relación temporada-Arsenal (si es método POST)
		if ($method == 'post') {
			// Se valida los datos de la relación del Arsenal con la temporada
			if (isset($postData['seasonarsenal'])) {
				$seasonArsenalModel = model('App\Models\SeasonArsenalModel');

				// Se omite la validación del id del arsenal
				$seasonArsenalModel->setValidationRule('arsenalId', 'permit_empty');
				unset($postData['seasonarsenal']['arsenalId']);

				$seasonArsenalResult = $seasonArsenalModel->validateRecord($postData['seasonarsenal'], isset($postFiles['seasonarsenal']) ? $postFiles['seasonarsenal'] : [], 'post');
				if ($seasonArsenalResult !== true) {
					$errors['seasonarsenal'] = $seasonArsenalResult;
				}
			}
		} else {
			unset($postData['seasonarsenal']);
		}

		return count($errors) > 0 ? $errors : true;
	}
}
