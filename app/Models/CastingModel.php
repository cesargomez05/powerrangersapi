<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class CastingModel extends Model
{
	use ModelTrait {
		list as public listTrait;
	}

	protected $table = 'casting';

	protected $allowedFields = ['serieId', 'seasonNumber', 'actorId', 'characterId', 'rangerId', 'isTeamUp'];

	protected $validationRules = [
		'serieId' => 'required|is_natural_no_zero|exists_id[series.id]',
		'seasonNumber' => 'required|is_natural_no_zero',
		'actorId' => 'required|is_natural_no_zero|exists_id[actors.id]',
		'characterId' => 'required|is_natural_no_zero|exists_id[characters.id]',
		'rangerId' => 'permit_empty|is_natural_no_zero|exists_id[rangers.id]',
		'isTeamUp' => 'required|is_natural|in_list[0,1]'
	];

	public function list($serieId, $seasonNumber, $query)
	{
		$this->setTable('view_casting');

		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('actorName', $query['q'], 'both');
			$this->orLike('characterName', $query['q'], 'both');
			$this->groupEnd();
		}

		return $this->listTrait($query);
	}

	public function get($serieId, $seasonNumber, $actorId, $characterId, $rangerId = null)
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

		$record = $this->findAll();
		return count($record) ? $record[0] : null;
	}

	public function insertRecord(&$record)
	{
		$this->db->transBegin();

		if (isset($record['actor'])) {
			$actorModel = new \App\Models\ActorModel();

			$actorResult = $actorModel->insertRecord($record['actor']);
			if ($actorResult !== true) {
				$this->db->transRollback();
				return $actorResult;
			}

			$record['actorId'] = $record['actor']['id'];
		}

		if (isset($record['character'])) {
			$characterModel = new \App\Models\CharacterModel();

			$characterResult = $characterModel->insertRecord($record['character']);
			if ($characterResult !== true) {
				$this->db->transRollback();
				return $characterResult;
			}

			$record['characterId'] = $record['character']['id'];
		}

		if (isset($record['ranger'])) {
			$rangerModel = new \App\Models\RangerModel();

			$rangerResult = $rangerModel->insertRecord($record['ranger'], true);
			if ($rangerResult !== true) {
				$this->db->transRollback();
				return $rangerResult;
			}

			$record['rangerId'] = $record['ranger']['id'];
		}

		$prevRecord = $this->get($record['serieId'], $record['seasonNumber'], $record['actorId'], $record['characterId'], $record['rangerId']);
		if (isset($prevRecord)) {
			return 'There one or more casting records with the same values';
		}

		$result = $this->insert($record);
		if ($result === false) {
			$this->db->transRollback();
			return $this->errors();
		}

		$this->db->transCommit();

		return true;
	}

	public function deleteRecord($serieId, $seasonNumber, $actorId, $characterId, $rangerId = null)
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

		if (!$this->delete()) {
			return $this->errors();
		}
		return true;
	}

	public function validateRecord(&$postData, $postFiles, $method, $prevRecord = null)
	{
		$errors = [];

		$this->validateRecordProperties($postData, $method, $prevRecord);

		if (isset($postData['actor'])) {
			$actorModel = new \App\Models\ActorModel();
			$actorModel->setValidationRule('actorId', 'permit_empty');
			unset($postData['actorId']);

			$actorErrors = $actorModel->validateRecord($postData['actor'], isset($postFiles['actor']) ? $postFiles['actor'] : [], 'post');
			if ($actorErrors !== true) {
				$errors['actor'] = $actorErrors;
			}
		}

		if (isset($postData['character'])) {
			$characterModel = new \App\Models\CharacterModel();
			$characterModel->setValidationRule('characterId', 'permit_empty');
			unset($postData['characterId']);

			$characterErrors = $characterModel->validateRecord($postData['character'], isset($postFiles['character']) ? $postFiles['character'] : [], 'post');
			if ($characterErrors !== true) {
				$errors['character'] = $characterErrors;
			}
		}

		if (isset($postData['ranger'])) {
			$rangerModel = new \App\Models\RangerModel();
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