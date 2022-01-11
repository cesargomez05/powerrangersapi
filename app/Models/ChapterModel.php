<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class ChapterModel extends Model
{
	use ModelTrait {
		list as public listTrait;
	}

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

	public function list($serieId, $seasonNumber, $query)
	{
		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber);
		if (isset($query['q']) && !empty($query['q']) && count($this->filterColumns) > 0) {
			$this->groupStart();
			$this->orLike('title', $query['q'], 'both');
			$this->orLike('titleSpanish', $query['q'], 'both');
			$this->groupEnd();
		}

		return $this->listTrait($query);
	}

	public function get($serieId, $seasonNumber, $number)
	{
		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber)->where('number', $number);
		$record = $this->findAll();
		return count($record) ? $record[0] : null;
	}

	public function insertRecord(&$record)
	{
		// Se procede a insertar el registro en la base de datos
		$recordId = $this->insert($record);
		if ($recordId === false) {
			return $this->errors();
		}

		return true;
	}

	public function updateRecord($record, $serieId, $seasonNumber, $number)
	{
		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber)->where('number', $number);

		$result = $this->update(null, $record);
		return $result === false ? $this->errors() : true;
	}

	public function deleteRecord($serieId, $seasonNumber, $number)
	{
		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber)->where('number', $number);
		if (!$this->delete()) {
			return $this->errors();
		}
		return true;
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

		return count($errors) > 0 ? $errors : true;
	}
}
