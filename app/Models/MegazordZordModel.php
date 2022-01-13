<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class MegazordZordModel extends Model
{
	use ModelTrait {
		list as public listTrait;
	}

	protected $table = 'megazord_zord';

	protected $allowedFields = ['megazordId', 'zordId'];

	protected $validationRules = [
		'megazordId' => 'required|is_natural_no_zero|exists_id[zords.id]',
		'zordId' => 'required|is_natural_no_zero|exists_id[zords.id]'
	];

	public function list($megazordId, $query)
	{
		$this->setTable('view_megazord_zord');

		$this->where('megazordId', $megazordId);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('zordName', $query['q'], 'both');
			$this->groupEnd();
		}

		return $this->listTrait($query);
	}

	public function get($megazordId, $zordId)
	{
		$this->where('megazordId', $megazordId)
			->where('zordId', $zordId);

		$record = $this->findAll();
		return count($record) ? $record[0] : null;
	}

	public function insertRecord(&$record)
	{
		$prevRecord = $this->get($record['megazordId'], $record['zordId']);
		if (isset($prevRecord)) {
			return 'There one or more megazord-zord relationship records';
		}

		// Se procede a insertar el registro en la base de datos
		$recordId = $this->insert($record);
		if ($recordId === false) {
			return $this->errors();
		}

		return true;
	}

	public function deleteRecord($megazordId, $zordId)
	{
		$this->where('megazordId', $megazordId)
			->where('zordId', $zordId);

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
