<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class MorpherModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
	}

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

	protected $returnType = \App\Entities\Morpher::class;

	protected function setRecordsCondition($query)
	{
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('name', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($id)
	{
		$this->where('id', $id);
	}

	protected function checkNestedRecords($id, &$errors)
	{
		$rangerModel = model('App\Models\RangerModel');
		if ($rangerModel->countByMorpherId($id)) {
			$errors['ranger'] = 'There are nested ranger records to morpher';
		}
	}

	protected function setPublicRecordsCondition($query)
	{
		$this->select(['name', 'slug slugURI', 'photo photoURI']);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('name', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setPublicRecordCondition($slug)
	{
		$this->select(['id', 'name', 'description', 'photo photoURI']);
		$this->where('slug', $slug);
	}

	protected function addRecordAttributes($morpher, $slug)
	{
		$rangerModel = model('App\Models\RangerModel');
		$morpher->rangers = $rangerModel->listByMorpher($morpher->id);

		unset($morpher->id);
	}

	public function insertRecord(&$record, $subTransaction = false)
	{
		if (!$subTransaction) {
			$this->db->transBegin();
		}

		$result = $this->insertRecordTrait($record);
		if ($result !== true) {
			$this->db->transRollback();
			return $result;
		}

		if (isset($record['rangersId'])) {
			$rangersId = explode(',', $record['rangersId']);

			$rangerModel = model('App\Models\RangerModel');
			$result = $rangerModel->builder()->whereIn('id', $rangersId)->update(['morpherId' => $record[$this->primaryKey]]);
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

		return empty($errors) ? true : $errors;
	}

	public function getByRanger($morpherId)
	{
		$this->select(['name', 'slug slugURI', 'photo photoURI']);
		$this->where('id', $morpherId);
		return $this->first();
	}
}
