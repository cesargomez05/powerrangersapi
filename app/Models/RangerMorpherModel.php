<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class RangerMorpherModel extends Model
{
	use ModelTrait;

	protected $table = 'ranger_morpher';

	protected $primaryKey = 'rangerId';

	protected $allowedFields = ['rangerId', 'morpherId', 'name', 'description', 'photo'];

	protected $validationRules = [
		'rangerId' => 'required|is_natural_no_zero|exists_id[rangers.id]',
		'morpherId' => 'required|is_natural_no_zero|exists_id[morphers.id]',
		'name' => 'permit_empty|max_length[100]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[25]'
	];

	protected $returnType = \App\Entities\RangerMorpher::class;

	protected function setRecordCondition($rangerId)
	{
		$this->where('rangerId', $rangerId);
	}

	public function listByMorpher($query, $morpherSlug)
	{
		$this->setTable('ranger_morpher_view');
		$this->select(['rangerSlug rangerURI', 'rangerName', 'photo photoURI']);
		$this->where('morpherSlug', $morpherSlug);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('rangerName', $query['q'], 'both');
			$this->groupEnd();
		}
		return $this->getResponse($query);
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

	public function insertRangerMorpher($morpherId, $rangersId)
	{
		$rangerMorpherRecords = array_map(function ($rangersId) use ($morpherId) {
			return ['rangerId' => $rangersId, 'morpherId' => $morpherId];
		}, explode(',', $rangersId));
		return $this->insertBatch($rangerMorpherRecords);
	}

	public function getByRanger($rangerSlug)
	{
		$this->setTable('ranger_morpher_view');
		$this->select(['morpherName', 'morpherSlug morpherURI', 'photo photoURI']);
		$this->where('rangerSlug', $rangerSlug);
		return $this->first();
	}
}
