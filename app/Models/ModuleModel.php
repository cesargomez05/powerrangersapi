<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class ModuleModel extends Model
{
	use ModelTrait;

	protected $table = 'modules';

	protected $allowedFields = ['id', 'name'];

	protected $validationRules = [
		'id' => 'required|max_length[50]|is_unique[modules.id,id,{_id}]',
		'name' => 'required|max_length[50]'
	];

	public function validateId($id, $property = 'id', $message = 'Id is not valid')
	{
		$validation = \Config\Services::validation();
		$validation->setRule($property, $message, 'required|max_length[50]');
		if ($validation->run([$property => $id])) {
			return true;
		} else {
			return $validation->getErrors();
		}
	}

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

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return count($errors) > 0 ? $errors : true;
	}
}
