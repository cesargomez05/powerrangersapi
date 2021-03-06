<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class SeasonArsenalModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
	}

	protected $table = 'season_arsenal';

	protected $allowedFields = ['serieId', 'seasonNumber', 'arsenalId', 'rangerId'];

	protected $validationRules = [
		'serieId' => 'required|is_natural_no_zero|exists_id[series.id]',
		'seasonNumber' => 'required|is_natural_no_zero',
		'arsenalId' => 'required|is_natural_no_zero|exists_id[arsenal.id]',
		'rangerId' => 'permit_empty|is_natural_no_zero|exists_id[rangers.id]',
		'seasonId' => 'check_id[seasonId,serieId,seasonNumber]|exists_record[seasonId,seasons,serieId,number]'
	];
	protected $validationMessages = [
		'seasonId' => [
			'check_id' => 'The \'serieId\' and \'seasonNumber\' values are required',
			'exists_record' => 'The season not exists'
		]
	];

	protected $returnType = \App\Entities\SeasonArsenal::class;

	protected function setRecordsCondition($query, $serieId, $seasonNumber)
	{
		$this->setTable('view_season_arsenal');
		$this->select(['arsenalId arsenalURI', 'arsenalName', 'rangerId rangerURI', 'rangerName']);
		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('arsenalName', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($serieId, $seasonNumber, $arsenalId)
	{
		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber)->where('arsenalId', $arsenalId);
	}

	protected function setPublicRecordsCondition($query, $serieSlug, $seasonNumber)
	{
		$this->setTable('season_arsenal_view');
		$this->select(['arsenalName', 'rangerName', 'arsenalSlug arsenalURI', 'rangerSlug rangerURI']);
		$this->where('serieSlug', $serieSlug);
		$this->where('seasonNumber', $seasonNumber);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('arsenalName', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	public function listByArsenal($query, $arsenalSlug)
	{
		$this->setTable('season_arsenal_view');
		$this->select(['CONCAT(serieSlug,\'/\',seasonNumber) seasonURI', 'serieTitle']);
		$this->where('arsenalSlug', $arsenalSlug);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('serieTitle', $query['q'], 'both');
			$this->groupEnd();
		}
		return $this->getResponse($query);
	}

	public function insertRecord(&$record)
	{
		$prevRecord = $this->check($record['serieId'], $record['seasonNumber'], $record['arsenalId']);
		if ($prevRecord) {
			return 'There one or more season-arsenal relationship records';
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
