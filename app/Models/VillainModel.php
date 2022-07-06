<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class VillainModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
	}

	protected $table = 'villains';

	protected $allowedFields = ['slug', 'name', 'description', 'photo'];

	protected $nestedModelClasses = [
		'seasonVillainModel' => 'App\Models\SeasonVillainModel'
	];

	protected $validationRules = [
		'slug' => 'required_with[name]|max_length[100]',
		'name' => 'required|max_length[100]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[25]'
	];

	protected $returnType = \App\Entities\Villain::class;

	protected function setRecordsCondition($query)
	{
		$this->select(['id URI', 'name', 'photo photoURI']);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('name', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($id)
	{
		$this->select(['*', 'photo photoURI']);
		$this->where('id', $id);
	}

	protected function checkNestedRecords($id, &$errors)
	{
		$seasonVillainModel = model($this->nestedModelClasses['seasonVillainModel']);
		if ($seasonVillainModel->countByVillainId($id)) {
			$errors['season_villain'] = 'There are nested season-villain records to villain';
		}
	}

	protected function setPublicRecordsCondition($query)
	{
		$this->select(['slug URI', 'name', 'photo photoURI']);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('name', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setPublicRecordCondition($slug)
	{
		$this->select(['name', 'description', 'photo photoURI']);
		$this->where('slug', $slug);
	}

	public function insertRecord(&$record)
	{
		$this->db->transBegin();

		// Se procede a insertar el registro en la base de datos
		$result = $this->insertRecordTrait($record);
		if ($result !== true) {
			$this->db->transRollback();
			return $result;
		}

		// Se inserta los datos de la relación Temporada-Villano (si aplica)
		if (isset($record['seasonvillain'])) {
			$record['seasonvillain']['villainId'] = $record[$this->primaryKey];

			$seasonVillainModel = model($this->nestedModelClasses['seasonVillainModel']);
			$seasonVillainResult = $seasonVillainModel->insertRecord($record['seasonvillain']);
			if ($seasonVillainResult !== true) {
				$this->db->transRollback();
				return $seasonVillainResult;
			}
		}

		$this->db->transCommit();

		return true;
	}

	public function validateRecord(&$postData, $postFiles, $method, $prevRecord = null)
	{
		$errors = $this->validateUploadFiles($postData, $postFiles);
		if ($errors === true) {
			$errors = [];
		}

		$this->validateRecordProperties($postData, $method, $prevRecord);

		$slugSettings = ['title' => 'name', 'field' => 'slug', 'id' => [$this->primaryKey]];
		$this->setSlugValue($postData, $slugSettings, isset($prevRecord) ? [$prevRecord[$this->primaryKey]] : null);

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		// Se valida los datos de la relación temporada-zord (si es método POST)
		if ($method == 'post') {
			// Se valida los datos de la relación del Zord con la temporada
			if (isset($postData['seasonvillain'])) {
				$seasonVillainModel = model($this->nestedModelClasses['seasonVillainModel']);

				// Se omite la validación del id del villano
				$seasonVillainModel->setValidationRule('villainId', 'permit_empty');
				unset($postData['seasonvillain']['villainId']);

				$seasonVillainResult = $seasonVillainModel->validateRecord($postData['seasonvillain'], isset($postFiles['seasonvillain']) ? $postFiles['seasonvillain'] : [], 'post');
				if ($seasonVillainResult !== true) {
					$errors['seasonvillain'] = $seasonVillainResult;
				}
			}
		} else {
			unset($postData['seasonvillain']);
		}

		return empty($errors) ? true : $errors;
	}
}
