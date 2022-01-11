<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class ActorModel extends Model
{
	use ModelTrait;

	protected $table = 'actors';

	protected $allowedFields = ['slug', 'name', 'birthDate', 'deathDate', 'photo'];

	protected $validationRules = [
		'slug' => 'required_with[name]|max_length[50]',
		'name' => 'required|max_length[50]',
		'birthDate' => 'permit_empty|valid_date[Y-m-d]',
		'deathDate' => 'permit_empty|valid_date[Y-m-d]',
		'photo' => 'permit_empty|max_length[100]'
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
		// Se procede a insertar el registro en la base de datos
		$recordId = $this->insert($record);
		if ($recordId === false) {
			return $this->errors();
		}

		if ($recordId !== 0) {
			$record[$this->primaryKey] = $recordId;
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

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return count($errors) > 0 ? $errors : true;
	}
}
