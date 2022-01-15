<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class CastingModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
	}

	protected $table = 'casting';

	protected $useAutoIncrement = false;

	protected $allowedFields = ['serieId', 'seasonNumber', 'actorId', 'characterId', 'rangerId', 'isTeamUp'];

	protected $validationRules = [
		'serieId' => 'required|is_natural_no_zero|exists_id[series.id]',
		'seasonNumber' => 'required|is_natural_no_zero',
		'actorId' => 'required|is_natural_no_zero|exists_id[actors.id]',
		'characterId' => 'required|is_natural_no_zero|exists_id[characters.id]',
		'rangerId' => 'permit_empty|is_natural_no_zero|exists_id[rangers.id]',
		'isTeamUp' => 'required|is_natural|in_list[0,1]',
		'seasonId' => 'check_id[seasonId,serieId,seasonNumber]|exists_record[seasonId,seasons,serieId,number]'
	];
	protected $validationMessages = [
		'seasonId' => [
			'check_id' => 'The \'serieId\' and \'seasonNumber\' values are required',
			'exists_record' => 'The season not exists'
		]
	];

	protected function setRecordsCondition($query, $serieId, $seasonNumber)
	{
		$this->setTable('view_casting');

		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('actorName', $query['q'], 'both');
			$this->orLike('characterName', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($serieId, $seasonNumber, $actorId, $characterId, $rangerId = null)
	{
		$this->where('serieId', $serieId)
			->where('seasonNumber', $seasonNumber)
			->where('actorId', $actorId)
			->where('characterId', $characterId);

		if (isset($rangerId)) {
			$this->where('rangerId', $rangerId);
		} else {
			$this->where('rangerId IS NULL');
		}
	}

	public function insertRecord(&$record)
	{
		$this->db->transBegin();

		if (isset($record['actor'])) {
			$actorModel = model('App\Models\ActorModel');

			$actorResult = $actorModel->insertRecord($record['actor']);
			if ($actorResult !== true) {
				$this->db->transRollback();
				return $actorResult;
			}

			$record['actorId'] = $record['actor']['id'];
		}

		if (isset($record['character'])) {
			$characterModel = model('App\Models\CharacterModel');

			$characterResult = $characterModel->insertRecord($record['character']);
			if ($characterResult !== true) {
				$this->db->transRollback();
				return $characterResult;
			}

			$record['characterId'] = $record['character']['id'];
		}

		if (isset($record['ranger'])) {
			$rangerModel = model('App\Models\RangerModel');

			$rangerResult = $rangerModel->insertRecord($record['ranger'], true);
			if ($rangerResult !== true) {
				$this->db->transRollback();
				return $rangerResult;
			}

			$record['rangerId'] = $record['ranger']['id'];
		}

		$prevRecord = $this->check($record['serieId'], $record['seasonNumber'], $record['actorId'], $record['characterId'], $record['rangerId']);
		if ($prevRecord) {
			return 'There one or more casting records with the same values';
		}

		$result = $this->insertRecordTrait($record);
		if ($result !== true) {
			$this->db->transRollback();
			return $result;
		}

		$this->db->transCommit();

		return true;
	}

	public function validateRecord(&$postData, $postFiles, $method, $prevRecord = null)
	{
		$errors = [];

		$this->validateRecordProperties($postData, $method, $prevRecord);

		if (isset($postData['actor'])) {
			$actorModel = model('App\Models\ActorModel');
			$actorModel->setValidationRule('actorId', 'permit_empty');
			unset($postData['actorId']);

			$actorErrors = $actorModel->validateRecord($postData['actor'], isset($postFiles['actor']) ? $postFiles['actor'] : [], 'post');
			if ($actorErrors !== true) {
				$errors['actor'] = $actorErrors;
			}
		}

		if (isset($postData['character'])) {
			$characterModel = model('App\Models\CharacterModel');
			$characterModel->setValidationRule('characterId', 'permit_empty');
			unset($postData['characterId']);

			$characterErrors = $characterModel->validateRecord($postData['character'], isset($postFiles['character']) ? $postFiles['character'] : [], 'post');
			if ($characterErrors !== true) {
				$errors['character'] = $characterErrors;
			}
		}

		if (isset($postData['ranger'])) {
			$rangerModel = model('App\Models\RangerModel');
			unset($postData['rangerId']);

			$rangerErrors = $rangerModel->validateRecord($postData['ranger'], isset($postFiles['ranger']) ? $postFiles['ranger'] : [], 'post');
			if ($rangerErrors !== true) {
				$errors['ranger'] = $rangerErrors;
			}
		}

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return count($errors) > 0 ? $errors : true;
	}
}
