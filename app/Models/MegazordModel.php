<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class MegazordModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
	}

	protected $table = 'megazords';

	protected $allowedFields = ['slug', 'name', 'description', 'photo'];

	protected $validationRules = [
		'slug' => 'required_with[name]|max_length[100]',
		'name' => 'required|max_length[100]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[100]'
	];

	protected $validationMessages = [
		'zordsId' => [
			'check_comma_separated' => 'Please set the zords id by comma separated',
			'validate_children_ids' => 'The zords ids not exist or are invalid'
		]
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
		if (isset($record['seasonmegazord'])) {
			$record['seasonmegazord']['megazordId'] = $record[$this->primaryKey];

			$seasonMegazordModel = model('App\Models\SeasonMegazordModel');
			$seasonMegazordResult = $seasonMegazordModel->insertRecord($record['seasonmegazord']);
			if ($seasonMegazordResult !== true) {
				$this->db->transRollback();
				return $seasonMegazordResult;
			}
		}

		// Se establece la información de los zords asociados al megazord (si aplica)
		if (isset($record['zordsId'])) {
			$zordsId = explode(',', $record['zordsId']);
			if (count($zordsId)) {
				$megazordZordModel = model('App\Models\MegazordZordModel');

				// Se asocia los Zords definidos en la lista sobre el Megazord creado
				$megazordZordResult = $megazordZordModel->insertZords($record[$this->primaryKey], $zordsId);
				if ($megazordZordResult === false) {
					$this->db->transRollback();
					return $megazordZordModel->errors();
				}
			}
		}

		$this->db->transCommit();

		return true;
	}

	protected function checkNestedRecords($id, &$errors)
	{
		$seasonMegazordModel = model('App\Models\SeasonMegazordModel');
		if ($seasonMegazordModel->countByMegazordId($id)) {
			$errors['season_megazord'] = 'There are nested season-megazord records to megazord';
		}

		$megazordZordModel = model('App\Models\MegazordZordModel');
		if ($megazordZordModel->countByMegazordId($id)) {
			$errors['megazord_zord'] = 'There are nested megazord-zord records to arsenal item';
		}
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

		if ($method == 'post') {
			$this->setValidationRule('zordsId', 'permit_empty|check_comma_separated|validate_children_ids[zords.id]');

			// Se valida los datos de la relación del Zord con la temporada
			if (isset($postData['seasonmegazord'])) {
				$seasonMegazordModel = model('App\Models\SeasonMegazordModel');

				// Se omite la validación del id del Zord
				$seasonMegazordModel->setValidationRule('megazordId', 'permit_empty');
				unset($postData['seasonmegazord']['megazordId']);

				$seasonMegazordErrors = $seasonMegazordModel->validateRecord($postData['seasonmegazord'], isset($postFiles['seasonmegazord']) ? $postFiles['seasonmegazord'] : [], 'post');
				if ($seasonMegazordErrors !== true) {
					$errors['seasonmegazord'] = $seasonMegazordErrors;
				}
			}
		} else {
			unset($postData['seasonmegazord']);
			unset($postData['zordsId']);
		}

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return count($errors) > 0 ? $errors : true;
	}
}
