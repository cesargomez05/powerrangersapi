<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class SeasonMegazordModel extends Model
{
	use ModelTrait {
		list as public listTrait;
	}

	protected $table = 'season_megazord';

	protected $allowedFields = ['serieId', 'seasonNumber', 'megazordId'];

	protected $validationRules = [
		'serieId' => 'required|is_natural_no_zero|exists_id[series.id]',
		'seasonNumber' => 'required|is_natural_no_zero',
		'megazordId' => 'required|is_natural_no_zero|exists_id[megazords.id]',
		'seasonId' => 'check_id[seasonId,serieId,seasonNumber]|exists_record[seasonId,seasons,serieId,number]'
	];
	protected $validationMessages = [
		'seasonId' => [
			'check_id' => 'The \'serieId\' and \'seasonNumber\' values are required',
			'exists_record' => 'The season not exists'
		]
	];

	public function list($serieId, $seasonNumber, $query)
	{
		$this->setTable('view_season_megazord');

		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('megazordName', $query['q'], 'both');
			$this->groupEnd();
		}

		return $this->listTrait($query);
	}

	public function get($serieId, $seasonNumber, $megazordId)
	{
		$this->where('serieId', $serieId)
			->where('seasonNumber', $seasonNumber)
			->where('megazordId', $megazordId);

		$record = $this->findAll();
		return count($record) ? $record[0] : null;
	}

	public function insertRecord(&$record)
	{
		$prevRecord = $this->get($record['serieId'], $record['seasonNumber'], $record['megazordId']);
		if (isset($prevRecord)) {
			return 'There one or more season-megazord relationship records';
		}

		// Se procede a insertar el registro en la base de datos
		$recordId = $this->insert($record);
		if ($recordId === false) {
			return $this->errors();
		}

		return true;
	}

	public function deleteRecord($serieId, $seasonNumber, $megazordId)
	{
		$this->where('serieId', $serieId)
			->where('seasonNumber', $seasonNumber)
			->where('megazordId', $megazordId);

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
