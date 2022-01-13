<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class MorpherModel extends Model
{
	use ModelTrait;

	protected $table = 'morphers';

	protected $allowedFields = ['slug', 'name', 'description', 'photo'];

	protected $validationRules = [
		'slug' => 'required_with[name]|max_length[100]',
		'name' => 'required|max_length[100]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[25]'
	];

	protected $validationMessages = [
		'rangersId' => [
			'check_comma_separated' => 'Please set the rangers id by comma separated',
			'validate_children_ids' => 'The rangers ids not exist or are invalid'
		]
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

		$recordId = $this->insert($record);
		if ($recordId === false) {
			$this->db->transRollback();
			return $this->errors();
		}

		if ($recordId !== 0) {
			$record[$this->primaryKey] = $recordId;
		}

		if (isset($record['rangersId'])) {
			$rangersId = explode(',', $record['rangersId']);

			$rangerModel = model('App\Models\RangerModel');
			$result = $rangerModel->builder()->whereIn('id', $rangersId)->update(['morpherId' => $recordId]);
			if ($result !== true) {
				$this->db->transRollback();
				return $rangerModel->errors();
			}
		}

		if (!$subTransaction) {
			$this->db->transCommit();
		}

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

		if ($method == 'post') {
			$this->setValidationRule('rangersId', 'permit_empty|check_comma_separated|validate_children_ids[rangers.id]');
		}

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return count($errors) > 0 ? $errors : true;
	}
}
