<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class ZordModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
	}

	protected $table = 'zords';

	protected $allowedFields = ['slug', 'name', 'description', 'photo'];

	protected $nestedModelClasses = [
		'seasonZordModel' => 'App\Models\SeasonZordModel'
	];

	protected $validationRules = [
		'slug' => 'required_with[name]|max_length[100]',
		'name' => 'required|max_length[100]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[100]'
	];

	protected function setRecordsCondition($query)
	{
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('name', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($id)
	{
		$this->where('id', $id);
	}

	protected function checkNestedRecords($id, &$errors)
	{
		$seasonZordModel = model($this->nestedModelClasses['seasonZordModel']);
		if ($seasonZordModel->countByZordId($id)) {
			$errors['season_zord'] = 'There are nested season-zord records to zord';
		}
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

		// Se inserta los datos de la relación Temporada-Zord (si aplica)
		if (isset($record['seasonzord'])) {
			$record['seasonzord']['zordId'] = $record[$this->primaryKey];

			$seasonZordModel = model($this->nestedModelClasses['seasonZordModel']);
			$seasonZordResult = $seasonZordModel->insertRecord($record['seasonzord']);
			if ($seasonZordResult !== true) {
				$this->db->transRollback();
				return $seasonZordResult;
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
			if (isset($postData['seasonzord'])) {
				$seasonZordModel = model($this->nestedModelClasses['seasonZordModel']);

				// Se omite la validación del id del Zord
				$seasonZordModel->setValidationRule('zordId', 'permit_empty');
				unset($postData['seasonzord']['zordId']);

				$seasonZordErrors = $seasonZordModel->validateRecord($postData['seasonzord'], isset($postFiles['seasonzord']) ? $postFiles['seasonzord'] : [], 'post');
				if ($seasonZordErrors !== true) {
					$errors['seasonzord'] = $seasonZordErrors;
				}
			}
		} else {
			unset($postData['seasonzord']);
		}

		return empty($errors) ? true : $errors;
	}
}
