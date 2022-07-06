<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class SeasonMegazordModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
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

	protected $returnType = \App\Entities\SeasonMegazord::class;

	protected function setRecordsCondition($query, $serieId, $seasonNumber)
	{
		$this->setTable('view_season_megazord');
		$this->select(['megazordId megazordURI', 'megazordName']);
		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('megazordName', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($serieId, $seasonNumber, $megazordId)
	{
		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber)->where('megazordId', $megazordId);
	}

	protected function setPublicRecordsCondition($query, $serieSlug, $seasonNumber)
	{
		$this->setTable('season_megazord_view');
		$this->select(['megazordSlug megazordURI', 'megazordName']);
		$this->where('serieSlug', $serieSlug);
		$this->where('seasonNumber', $seasonNumber);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('megazordName', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	public function insertRecord(&$record)
	{
		$prevRecord = $this->check($record['serieId'], $record['seasonNumber'], $record['megazordId']);
		if ($prevRecord) {
			return 'There one or more season-megazord relationship records';
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
}
