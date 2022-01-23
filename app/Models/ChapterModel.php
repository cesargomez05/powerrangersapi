<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class ChapterModel extends Model
{
	use ModelTrait;

	protected $table = 'chapters';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['serieId', 'seasonNumber', 'number', 'slug', 'title', 'titleSpanish', 'summary'];

	protected $validationRules = [
		'serieId' => 'required|is_natural_no_zero|exists_id[series.id]',
		'seasonNumber' => 'required|is_natural_no_zero',
		'number' => 'required|is_natural_no_zero',
		'slug' => 'required_with[title]|max_length[100]',
		'title' => 'required|max_length[100]',
		'titleSpanish' => 'required|max_length[100]',
		'summary' => 'permit_empty'
	];

	protected $returnType = \App\Entities\Chapter::class;

	protected function setRecordsCondition($query, $serieId, $seasonNumber)
	{
		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('title', $query['q'], 'both');
			$this->orLike('titleSpanish', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($serieId, $seasonNumber, $number)
	{
		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber)->where('number', $number);
	}

	protected function setPublicRecordsCondition($query, $serieSlug, $seasonNumber)
	{
		$this->setTable('chapters_view');
		$this->select(['number', 'title', 'titleSpanish', 'CONCAT(serieSlug,\'/\',seasonNumber,\'/\',number) slugURI']);
		$this->where('serieSlug', $serieSlug);
		$this->where('seasonNumber', $seasonNumber);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('title', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setPublicRecordCondition($serieSlug, $seasonNumber, $number)
	{
		$this->setTable('chapters_view');
		$this->select(['number', 'title', 'titleSpanish', 'summary']);
		$this->where('serieSlug', $serieSlug);
		$this->where('seasonNumber', $seasonNumber);
		$this->where('number', $number);
	}

	public function validateRecord(&$postData, $postFiles, $method, $prevRecord = null)
	{
		$errors = [];

		$this->validateRecordProperties($postData, $method, $prevRecord);

		$slugSettings = ['title' => 'title', 'field' => 'slug', 'id' => ['serieId', 'seasonNumber', 'number']];
		$this->setSlugValue($postData, $slugSettings, isset($prevRecord) ? [$prevRecord['serieId'], $prevRecord['seasonNumber'], $prevRecord['number']] : null);

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return empty($errors) ? true : $errors;
	}

	public function countBySeason($serieSlug, $seasonNumber)
	{
		$this->setTable('chapters_view');
		$this->where('serieSlug', $serieSlug);
		$this->where('seasonNumber', $seasonNumber);
		return $this->countAllResults();
	}
}
