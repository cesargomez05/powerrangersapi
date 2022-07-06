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

	protected $allowedFields = ['serieId', 'seasonNumber', 'actorId', 'characterId', 'rangerId', 'isTeamUp'];

	protected $validationRules = [
		'serieId' => 'required|is_natural_no_zero|exists_id[series.id]',
		'seasonNumber' => 'required|is_natural_no_zero',
		'actorId' => 'permit_empty|is_natural_no_zero|exists_id[actors.id]',
		'characterId' => 'permit_empty|is_natural_no_zero|exists_id[characters.id]',
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

	protected $returnType = \App\Entities\Casting::class;

	protected function setRecordsCondition($query, $serieId, $seasonNumber)
	{
		$this->setTable('view_casting');
		$this->select(['actorId', 'characterId', 'rangerId', 'isTeamUp', 'actorName', 'characterName', 'rangerName']);
		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('actorName', $query['q'], 'both');
			$this->orLike('characterName', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($serieId, $seasonNumber, $actorId = null, $characterId = null, $rangerId = null)
	{
		// Condición de serie y temporada
		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber);

		isset($actorId) ? $this->where('actorId', $actorId) : $this->where('actorId IS NULL');
		isset($characterId) ? $this->where('characterId', $characterId) : $this->where('characterId IS NULL');
		isset($rangerId) ? $this->where('rangerId', $rangerId) : $this->where('rangerId IS NULL');
	}

	protected function setPublicRecordsCondition($query, $serieSlug, $seasonNumber)
	{
		$this->setTable('casting_view');
		$this->select(['actorName', 'actorSlug actorURI', 'characterName', 'characterSlug characterURI', 'rangerName', 'rangerSlug rangerURI']);
		$this->where('serieSlug', $serieSlug);
		$this->where('seasonNumber', $seasonNumber);
		$this->where('isTeamUp', 0);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('actorName', $query['q'], 'both');
			$this->orLike('characterName', $query['q'], 'both');
			$this->orLike('rangerName', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	public function listTeamUpPublic($query, $serieSlug, $seasonNumber)
	{
		$this->setTable('casting_view');
		$this->select(['actorSlug actorURI', 'actorName', 'characterSlug characterURI', 'characterName', 'rangerSlug rangerURI', 'rangerName']);
		$this->where('serieSlug', $serieSlug);
		$this->where('seasonNumber', $seasonNumber);
		$this->where('isTeamUp', 1);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('actorName', $query['q'], 'both');
			$this->orLike('characterName', $query['q'], 'both');
			$this->orLike('rangerName', $query['q'], 'both');
			$this->groupEnd();
		}
		return $this->getResponse($query);
	}

	public function listByActor($query, $actorSlug)
	{
		$this->setTable('casting_view');
		$this->select(['CONCAT(serieSlug,\'/\',seasonNumber) seasonURI', 'serieTitle', 'seasonNumber', 'isTeamUp', 'characterSlug characterURI', 'characterName', 'rangerSlug rangerURI', 'rangerName']);
		$this->where('actorSlug', $actorSlug);
		return $this->getResponse($query);
	}

	public function listByCharacter($query, $characterSlug)
	{
		$this->setTable('casting_view');
		$this->select(['CONCAT(serieSlug,\'/\',seasonNumber) seasonURI', 'serieTitle', 'seasonNumber', 'isTeamUp', 'actorSlug actorURI', 'actorName', 'rangerSlug rangerURI', 'rangerName']);
		$this->where('characterSlug', $characterSlug);
		return $this->getResponse($query);
	}

	public function listByRanger($query, $rangerSlug)
	{
		$this->setTable('casting_view');
		$this->select(['CONCAT(serieSlug,\'/\',seasonNumber) seasonURI', 'serieTitle', 'seasonNumber', 'isTeamUp', 'actorSlug actorURI', 'actorName', 'characterSlug characterURI', 'characterName']);
		$this->where('rangerSlug', $rangerSlug);
		return $this->getResponse($query);
	}

	public function insertRecord(&$record)
	{
		$this->db->transBegin();

		$actorResult = $this->handleInsertActor($record);
		if (isset($actorResult)) {
			$this->db->transRollback();
			return $actorResult;
		}

		$characterResult = $this->handleInsertCharacter($record);
		if (isset($characterResult)) {
			$this->db->transRollback();
			return $characterResult;
		}

		$rangerResult = $this->handleInsertRanger($record);
		if (isset($rangerResult)) {
			$this->db->transRollback();
			return $rangerResult;
		}

		$prevRecord = $this->check($record['serieId'], $record['seasonNumber'], $record['actorId'], $record['characterId'], $record['rangerId']);
		if ($prevRecord) {
			$this->db->transRollback();
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

		$this->handleValidateActor($postData, $postFiles, $errors);
		$this->handleValidateCharacter($postData, $postFiles, $errors);
		$this->handleValidateRanger($postData, $postFiles, $errors);

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return empty($errors) ? true : $errors;
	}

	private function handleInsertActor(&$record)
	{
		if (isset($record['actor'])) {
			$actorModel = model('App\Models\ActorModel');

			$actorResult = $actorModel->insertRecord($record['actor']);
			if ($actorResult !== true) {
				return $actorResult;
			}

			$record['actorId'] = $record['actor']['id'];
		}
	}

	private function handleInsertCharacter(&$record)
	{
		if (isset($record['character'])) {
			$characterModel = model('App\Models\CharacterModel');

			$characterResult = $characterModel->insertRecord($record['character']);
			if ($characterResult !== true) {
				return $characterResult;
			}

			$record['characterId'] = $record['character']['id'];
		}
	}

	private function handleInsertRanger(&$record)
	{
		if (isset($record['ranger'])) {
			$rangerModel = model('App\Models\RangerModel');

			$rangerResult = $rangerModel->insertRecord($record['ranger'], true);
			if ($rangerResult !== true) {
				return $rangerResult;
			}

			$record['rangerId'] = $record['ranger']['id'];
		}
	}

	private function handleValidateActor(&$postData, $postFiles, &$errors)
	{
		if (isset($postData['actor'])) {
			$actorModel = model('App\Models\ActorModel');
			unset($postData['actorId']);

			$actorErrors = $actorModel->validateRecord($postData['actor'], isset($postFiles['actor']) ? $postFiles['actor'] : [], 'post');
			if ($actorErrors !== true) {
				$errors['actor'] = $actorErrors;
			}
		}
	}

	private function handleValidateCharacter(&$postData, $postFiles, &$errors)
	{
		if (isset($postData['character'])) {
			$characterModel = model('App\Models\CharacterModel');
			unset($postData['characterId']);

			$characterErrors = $characterModel->validateRecord($postData['character'], isset($postFiles['character']) ? $postFiles['character'] : [], 'post');
			if ($characterErrors !== true) {
				$errors['character'] = $characterErrors;
			}
		}
	}

	private function handleValidateRanger(&$postData, $postFiles, &$errors)
	{
		if (isset($postData['ranger'])) {
			$rangerModel = model('App\Models\RangerModel');
			unset($postData['rangerId']);

			$rangerErrors = $rangerModel->validateRecord($postData['ranger'], isset($postFiles['ranger']) ? $postFiles['ranger'] : [], 'post');
			if ($rangerErrors !== true) {
				$errors['ranger'] = $rangerErrors;
			}
		}
	}
}
