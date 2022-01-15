<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class CharacterModel extends Model
{
	use ModelTrait;

	protected $table = 'characters';

	protected $allowedFields = ['slug', 'name', 'fullName', 'description', 'photo'];

	protected $validationRules = [
		'slug' => 'required_with[name]|max_length[50]',
		'name' => 'required|max_length[50]',
		'fullName' => 'permit_empty|max_length[150]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[100]'
	];

	protected function setRecordsCondition($query)
	{
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('name', $query['q'], 'both');
			$this->orLike('fullName', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($id)
	{
		$this->where('id', $id);
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
