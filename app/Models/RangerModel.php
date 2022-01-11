<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class RangerModel extends Model
{
	use ModelTrait;

	protected $table = 'rangers';

	protected $allowedFields = ['slug', 'name', 'description', 'photo', 'morpherId'];

	protected $validationRules = [
		'slug' => 'required_with[name]|max_length[100]',
		'name' => 'required|max_length[100]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[100]',
		'morpherId' => 'permit_empty|is_natural_no_zero|exists_id[morphers.id]'
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

	public function insertRecord(&$record, $subTransaction = false)
	{
		if (!$subTransaction) {
			$this->db->transBegin();
		}

		if (isset($record['morpher'])) {
			$morpherModel = new \App\Models\MorpherModel();

			$morpherResult = $morpherModel->insertRecord($record['morpher'], true);
			if ($morpherResult !== true) {
				$this->db->transRollback();
				return $morpherResult;
			}

			$record['morpherId'] = $record['morpher']['id'];
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

	public function updateRecord($record, $id)
	{
		$this->db->transBegin();

		if (isset($record['morpher'])) {
			$morpherModel = new \App\Models\MorpherModel();

			$morpherResult = $morpherModel->insertRecord($record['morpher']);
			if ($morpherResult !== true) {
				$this->db->transRollback();
				return $morpherResult;
			}

			$record['morpherId'] = $record['morpher']['id'];
		}

		$this->where('id', $id);
		$result = $this->update(null, $record);
		if ($result !== true) {
			$this->db->transRollback();
			return $this->errors();
		}

		$this->db->transCommit();

		return true;
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

		// Se valida los datos del Morpher
		if (isset($postData['morpher'])) {
			// Se elimina el Id del morpher y los Ids de rangers a asociar al morpher (Si aplica)
			unset($postData['morpherId']);
			unset($postData['morpher']['rangersId']);

			$morpherModel = new \App\Models\MorpherModel();
			$morpherErrors = $morpherModel->validateRecord($postData['morpher'], isset($postFiles['morpher']) ? $postFiles['morpher'] : [], 'post');
			if ($morpherErrors !== true) {
				$errors['morpher'] = $morpherErrors;
			}
		}

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return count($errors) > 0 ? $errors : true;
	}
}
