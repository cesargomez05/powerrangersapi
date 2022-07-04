<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class SeasonModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
		validateId as public validateIdTrait;
	}

	protected $table = 'seasons';

	protected $allowedFields = ['serieId', 'number', 'year', 'title', 'ageId', 'synopsis'];

	protected $validationRules = [
		'serieId' => 'required|is_natural_no_zero|exists_id[series.id]',
		'number' => 'required|is_natural_no_zero',
		'year' => 'permit_empty|is_year',
		'title' => 'permit_empty|max_length[50]',
		'synopsis' => 'permit_empty',
		'ageId' => 'required|is_natural_no_zero|exists_id[ages.id]'
	];

	protected $returnType = \App\Entities\Season::class;

	protected function setRecordsCondition($query, $serieId)
	{
		$this->where('serieId', $serieId);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('title', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($serieId, $number)
	{
		$this->where('serieId', $serieId)->where('number', $number);
	}

	protected function checkNestedRecords($serieId, $seasonNumber, &$errors)
	{
		$castingModel = model('App\Models\CastingModel');
		if ($castingModel->countBySeasonId($serieId, $seasonNumber)) {
			$errors['casting'] = 'There are nested casting records to season';
		}
		$chapterModel = model('App\Models\ChapterModel');
		if ($chapterModel->countBySeasonId($serieId, $seasonNumber)) {
			$errors['chapter'] = 'There are nested chapter records to season';
		}
		$seasonArsenalModel = model('App\Models\SeasonArsenalModel');
		if ($seasonArsenalModel->countBySeasonId($serieId, $seasonNumber)) {
			$errors['season_arsenal'] = 'There are nested season-arsenal records to season';
		}
		$seasonMegazordModel = model('App\Models\SeasonMegazordModel');
		if ($seasonMegazordModel->countBySeasonId($serieId, $seasonNumber)) {
			$errors['season_megazord'] = 'There are nested season-megazord records to season';
		}
		$seasonVillainModel = model('App\Models\SeasonVillainModel');
		if ($seasonVillainModel->countBySeasonId($serieId, $seasonNumber)) {
			$errors['season_villain'] = 'There are nested season-villain records to season';
		}
		$seasonZordModel = model('App\Models\SeasonZordModel');
		if ($seasonZordModel->countBySeasonId($serieId, $seasonNumber)) {
			$errors['season_zord'] = 'There are nested season-zord records to season';
		}
	}

	protected function setPublicRecordsCondition($query, $serieSlug)
	{
		$this->setTable('seasons_view');
		$this->select(['number', 'year', 'title', 'CONCAT(serieSlug,\'/\',number) slugURI']);
		$this->where('serieSlug', $serieSlug);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('title', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setPublicRecordCondition($serieSlug, $number)
	{
		$this->setTable('seasons_view');
		$this->select(['number', 'year', 'title', 'ageName', 'synopsis', 'CONCAT(serieSlug,\'/\',number) chapterSlugURI', 'CONCAT(serieSlug,\'/\',number) castingSlugURI', 'CONCAT(serieSlug,\'/\',number) teamupSlugURI']);
		$this->where('serieSlug', $serieSlug);
		$this->where('number', $number);
	}

	protected function addRecordAttributes($season, $serieSlug, $slug)
	{
		$chapterModel = model('App\Models\ChapterModel');
		$season->chapters = $chapterModel->countBySeason($serieSlug, $slug);
	}

	public function insertRecord(&$record, $subTransaction = false)
	{
		if (!$subTransaction) {
			$this->db->transBegin();
		}

		if (isset($record['age'])) {
			$ageModel = model('App\Models\AgeModel');
			$ageResult = $ageModel->insertRecord($record['age']);
			if ($ageResult !== true) {
				$this->db->transRollback();
				return $ageResult;
			}

			$record['ageId'] = $record['age']['id'];
		}

		// Se procede a insertar el registro en la base de datos
		$result = $this->insertRecordTrait($record);
		if ($result !== true) {
			$this->db->transRollback();
			return $result;
		}

		if (!$subTransaction) {
			$this->db->transCommit();
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

		// Se valida los datos de la era
		if (isset($postData['age'])) {
			// Se omite la validación del Id de la era
			$this->setValidationRule('ageId', 'permit_empty');
			unset($postData['ageId']);

			$ageModel = model('App\Models\AgeModel');
			$ageErrors = $ageModel->validateRecord($postData['age'], isset($postFiles['age']) ? $postFiles['age'] : [], 'post');
			if ($ageErrors !== true) {
				$errors['age'] = $ageErrors;
			}
		}

		return empty($errors) ? true : $errors;
	}

	public function listByAge($ageSlug)
	{
		$this->setTable('seasons_view');
		$this->select(['title', 'number', 'year', 'CONCAT(serieSlug,\'/\',number) slugURI']);
		$this->where('ageSlug', $ageSlug);
		return $this->findAll();
	}

	public function countBySerie($serieSlug)
	{
		$this->setTable('seasons_view');
		$this->where('serieSlug', $serieSlug);
		return $this->countAllResults();
	}
}
