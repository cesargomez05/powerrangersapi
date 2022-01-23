<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class TransformationRangerModel extends Model
{
	use ModelTrait {
		list as listTrait;
	}

	protected $table = 'transformation_ranger';

	protected $allowedFields = ['transformationId', 'rangerId', 'name', 'description', 'photo'];

	protected $validationRules = [
		'transformationId' => 'required|is_natural_no_zero|exists_id[actors.id]',
		'rangerId' => 'required|is_natural_no_zero|exists_id[characters.id]',
		'name' => 'permit_empty|max_length[100]',
		'photo' => 'permit_empty|max_length[100]'
	];

	protected $returnType = \App\Entities\TransformationRanger::class;

	protected function setRecordsCondition($query, $transformationId)
	{
		$this->setTable('view_transformation_ranger');

		$this->where('transformationId', $transformationId);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('name', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($transformationId, $rangerId)
	{
		$this->where('transformationId', $transformationId)->where('rangerId', $rangerId);
	}

	public function validateRecord(&$postData, $postFiles, $method, $prevRecord = null)
	{
		$errors = $this->validateUploadFiles($postData, $postFiles);
		if ($errors === true) {
			$errors = [];
		}

		$this->validateRecordProperties($postData, $method, $prevRecord);

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return empty($errors) ? true : $errors;
	}

	public function insertTransformationRangers($transformationId, $rangers)
	{
		$this->setValidationRule('transformationId', 'permit_empty');

		foreach ($rangers as &$ranger) {
			$ranger['transformationId'] = $transformationId;
		}

		return $this->insertBatch($rangers);
	}

	public function listByTransformation($transformationSlug)
	{
		$this->setTable('transformation_ranger_view');
		$this->select(['rangerName', 'rangerSlug rangerSlugURI', 'transformationName', 'photo photoURI']);
		$this->where('transformationSlug', $transformationSlug);
		return $this->findAll();
	}

	public function listByRanger($rangerSlug)
	{
		$this->setTable('transformation_ranger_view');
		$this->select(['transformationName', 'transformationSlug transformationSlugURI', 'photo photoURI']);
		$this->where('rangerSlug', $rangerSlug);
		return $this->findAll();
	}
}
