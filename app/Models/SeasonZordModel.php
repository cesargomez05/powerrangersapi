<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class SeasonZordModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
	}

	protected $table = 'season_zord';

	protected $allowedFields = ['serieId', 'seasonNumber', 'zordId', 'rangerId'];

	protected $validationRules = [
		'serieId' => 'required|is_natural_no_zero|exists_id[series.id]',
		'seasonNumber' => 'required|is_natural_no_zero',
		'zordId' => 'required|is_natural_no_zero|exists_id[zords.id]',
		'rangerId' => 'permit_empty|is_natural_no_zero|exists_id[rangers.id]',
		'seasonId' => 'check_id[seasonId,serieId,seasonNumber]|exists_record[seasonId,seasons,serieId,number]'
	];
	protected $validationMessages = [
		'seasonId' => [
			'check_id' => 'The \'serieId\' and \'seasonNumber\' values are required',
			'exists_record' => 'The season not exists'
		]
	];

	protected $returnType = \App\Entities\SeasonZord::class;

	protected function setRecordsCondition($query, $serieId, $seasonNumber)
	{
		$this->setTable('view_season_zord');

		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('zordName', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($serieId, $seasonNumber, $zordId)
	{
		$this->where('serieId', $serieId)
			->where('seasonNumber', $seasonNumber)
			->where('zordId', $zordId);
	}

	public function insertRecord(&$record)
	{
		$prevRecord = $this->check($record['serieId'], $record['seasonNumber'], $record['zordId']);
		if ($prevRecord) {
			return 'There one or more season-zord relationship records';
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

	public function listByZord($zordSlug)
	{
		$this->setTable('season_zord_view');
		$this->select(['serieTitle', 'seasonNumber', 'CONCAT(serieSlug,\'/\',seasonNumber) seasonSlugURI']);
		$this->where('zordSlug', $zordSlug);
		return $this->findAll();
	}
}
