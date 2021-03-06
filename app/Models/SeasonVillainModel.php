<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class SeasonVillainModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
	}

	protected $table = 'season_villain';

	protected $allowedFields = ['serieId', 'seasonNumber', 'villainId'];

	protected $validationRules = [
		'serieId' => 'required|is_natural_no_zero|exists_id[series.id]',
		'seasonNumber' => 'required|is_natural_no_zero',
		'villainId' => 'required|is_natural_no_zero|exists_id[villains.id]',
		'seasonId' => 'check_id[seasonId,serieId,seasonNumber]|exists_record[seasonId,seasons,serieId,number]'
	];
	protected $validationMessages = [
		'seasonId' => [
			'check_id' => 'The \'serieId\' and \'seasonNumber\' values are required',
			'exists_record' => 'The season not exists'
		]
	];

	protected $returnType = \App\Entities\SeasonVillain::class;

	protected function setRecordsCondition($query, $serieId, $seasonNumber)
	{
		$this->setTable('view_season_villain');
		$this->select(['villainId villainURI', 'villainName']);
		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('villainName', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($serieId, $seasonNumber, $villainId)
	{
		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber)->where('villainId', $villainId);
	}

	protected function setPublicRecordsCondition($query, $serieSlug, $seasonNumber)
	{
		$this->setTable('season_villain_view');
		$this->select(['villainSlug villainURI', 'villainName']);
		$this->where('serieSlug', $serieSlug);
		$this->where('seasonNumber', $seasonNumber);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('villainName', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	public function listByVillain($query, $villainSlug)
	{
		$this->setTable('season_villain_view');
		$this->select(['CONCAT(serieSlug,\'/\',seasonNumber) seasonURI', 'serieTitle']);
		$this->where('villainSlug', $villainSlug);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('serieTitle', $query['q'], 'both');
			$this->groupEnd();
		}
		return $this->getResponse($query);
	}

	public function insertRecord(&$record)
	{
		$prevRecord = $this->check($record['serieId'], $record['seasonNumber'], $record['villainId']);
		if (isset($prevRecord)) {
			return 'There one or more season-villain relationship records';
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
