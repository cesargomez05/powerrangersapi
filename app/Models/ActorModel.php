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
		'photo' => 'permit_empty|max_length[25]'
	];

	protected $returnType = \App\Entities\Actor::class;

	protected function setRecordsCondition($query)
	{
		$this->select(['id URI', 'name', 'photo photoURI']);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('name', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($id)
	{
		$this->select(['*', 'photo photoURI']);
		$this->where('id', $id);
	}

	protected function checkNestedRecords($id, &$errors)
	{
		$castingModel = model('App\Models\CastingModel');
		if ($castingModel->countByActorId($id)) {
			$errors['casting'] = 'There are nested casting records to actor';
		}
	}

	protected function setPublicRecordsCondition($query)
	{
		$this->select(['slug URI', 'name', 'photo photoURI']);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('name', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setPublicRecordCondition($slug)
	{
		$this->select(['name', 'birthDate', 'deathDate', 'photo photoURI', 'slug actorCastingURI']);
		$this->where('slug', $slug);
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

		return empty($errors) ? true : $errors;
	}
}
