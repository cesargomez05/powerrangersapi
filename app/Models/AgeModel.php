<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class AgeModel extends Model
{
	use ModelTrait;

	protected $table = 'ages';

	protected $allowedFields = ['slug', 'name'];

	protected $validationRules = [
		'slug' => 'required_with[name]|max_length[20]',
		'name' => 'required|max_length[20]'
	];

	protected $returnType = \App\Entities\Age::class;

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
		$seasonModel = model('App\Models\SeasonModel');
		if ($seasonModel->countByAgeId($id)) {
			$errors['season'] = 'There are nested season records to age';
		}
	}

	protected function setPublicRecordsCondition($query)
	{
		$this->select(['name', 'slug AS slugURI']);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('name', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setPublicRecordCondition($slug)
	{
		$this->select(['name', 'slug AS slugURI']);
		$this->where('slug', $slug);
	}

	protected function addRecordAttributes($age, $slug)
	{
		$age->seasons = [];
	}

	public function validateRecord(&$postData, $postFiles, $method, $prevRecord = null)
	{
		$errors = [];

		$this->validateRecordProperties($postData, $method, $prevRecord);

		$slugSettings = ['title' => 'name', 'field' => 'slug', 'id' => [$this->primaryKey]];
		$this->setSlugValue($postData, $slugSettings, isset($prevRecord) ? [$prevRecord[$this->primaryKey]] : null);

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return empty($errors) ? true : $errors;
	}
}
