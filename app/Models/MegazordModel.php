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

	protected $nestedModelClasses = [
		'seasonMegazordModel' => 'App\Models\SeasonMegazordModel'
	];

	protected $validationRules = [
		'slug' => 'required_with[name]|max_length[100]',
		'name' => 'required|max_length[100]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[25]'
	];

	protected $validationMessages = [
		'zordsId' => [
			'check_comma_separated' => 'Please set the zords id by comma separated',
			'validate_children_ids' => 'The zords ids not exist or are invalid'
		]
	];

	protected $returnType = \App\Entities\Megazord::class;

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

			$seasonMegazordModel = model($this->nestedModelClasses['seasonMegazordModel']);
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
		$seasonMegazordModel = model($this->nestedModelClasses['seasonMegazordModel']);
		if ($seasonMegazordModel->countByMegazordId($id)) {
			$errors['season_megazord'] = 'There are nested season-megazord records to megazord';
		}

		$megazordZordModel = model('App\Models\MegazordZordModel');
		if ($megazordZordModel->countByMegazordId($id)) {
			$errors['megazord_zord'] = 'There are nested megazord-zord records to arsenal item';
		}
	}

	protected function setPublicRecordsCondition($query)
	{
		$this->select(['name', 'slug slugURI', 'photo photoURI']);
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

	protected function addRecordAttributes($megazord, $slug)
	{
		$seasonMegazordModel = model('App\Models\SeasonMegazordModel');
		$megazord->seasons = $seasonMegazordModel->listByMegazord($slug);
		$megazordZordModel = model('App\Models\MegazordZordModel');
		$megazord->zords = $megazordZordModel->listByMegazord($slug);
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
				$seasonMegazordModel = model($this->nestedModelClasses['seasonMegazordModel']);

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

		return empty($errors) ? true : $errors;
	}
}
