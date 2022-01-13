<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class SeasonArsenalModel extends Model
{
	use ModelTrait {
		list as public listTrait;
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

	public function list($serieId, $seasonNumber, $query)
	{
		$this->setTable('view_season_arsenal');

		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('arsenalName', $query['q'], 'both');
			$this->groupEnd();
		}

		return $this->listTrait($query);
	}

	public function get($serieId, $seasonNumber, $arsenalId)
	{
		$this->where('serieId', $serieId)
			->where('seasonNumber', $seasonNumber)
			->where('arsenalId', $arsenalId);

		$record = $this->findAll();
		return count($record) ? $record[0] : null;
	}

	public function insertRecord(&$record)
	{
		$prevRecord = $this->get($record['serieId'], $record['seasonNumber'], $record['arsenalId']);
		if (isset($prevRecord)) {
			return 'There one or more season-arsenal relationship records';
		}

		// Se procede a insertar el registro en la base de datos
		$recordId = $this->insert($record);
		if ($recordId === false) {
			return $this->errors();
		}

		return true;
	}

	public function deleteRecord($serieId, $seasonNumber, $arsenalId)
	{
		$this->where('serieId', $serieId)
			->where('seasonNumber', $seasonNumber)
			->where('arsenalId', $arsenalId);

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
