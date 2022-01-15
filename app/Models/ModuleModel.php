<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class ModuleModel extends Model
{
	use ModelTrait;

	protected $table = 'modules';

	protected $useAutoIncrement = false;

	protected $allowedFields = ['id', 'name'];

	protected $validationRules = [
		'id' => 'required|max_length[50]|is_unique[modules.id,id,{_id}]',
		'name' => 'required|max_length[50]'
	];

	protected $rulesId = 'required|max_length[50]';

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
