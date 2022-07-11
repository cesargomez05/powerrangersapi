<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class MegazordZordModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
	}

	protected $table = 'megazord_zord';

	protected $allowedFields = ['megazordId', 'zordId'];

	protected $validationRules = [
		'megazordId' => 'required|is_natural_no_zero|exists_id[zords.id]',
		'zordId' => 'required|is_natural_no_zero|exists_id[zords.id]'
	];

	protected $returnType = \App\Entities\MegazordZord::class;

	protected function setRecordsCondition($query, $megazordId)
	{
		$this->setTable('view_megazord_zord');
		$this->select(['zordId zordURI', 'zordName']);
		$this->where('megazordId', $megazordId);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('zordName', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($megazordId, $zordId)
	{
		$this->where('megazordId', $megazordId)->where('zordId', $zordId);
	}

	protected function setPublicRecordsCondition($query, $megazordSlug)
	{
		$this->setTable('megazord_zord_view');
		$this->select(['zordSlug zordURI', 'zordName']);
		$this->where('megazordSlug', $megazordSlug);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('zordName', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	public function listByZord($query, $zordSlug)
	{
		$this->setTable('megazord_zord_view');
		$this->select(['megazordSlug megazordURI', 'megazordName']);
		$this->where('zordSlug', $zordSlug);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('megazordName', $query['q'], 'both');
			$this->groupEnd();
		}
		return $this->getResponse($query);
	}

	public function insertRecord(&$record)
	{
		$prevRecord = $this->check($record['megazordId'], $record['zordId']);
		if ($prevRecord) {
			return 'There one or more megazord-zord relationship records';
		}

		// Se procede a insertar el registro en la base de datos
		return $this->insertRecordTrait($record);
	}

	public function validateRecord(&$postData, $postFiles, $method, $prevRecord = null)
	{
		$errors = [];

		$this->validateRecordProperties($postData, $method, $prevRecord);

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return empty($errors) ? true : $errors;
	}

	public function insertZords($megazordId, $zordsId)
	{
		$this->setValidationRule('megazordId', 'permit_empty');

		$megazordZords = array_map(function ($zordId) use ($megazordId) {
			return [
				'megazordId' => $megazordId,
				'zordId' => $zordId
			];
		}, $zordsId);

		return $this->insertBatch($megazordZords);
	}
}
