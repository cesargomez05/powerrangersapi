<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class RangerModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
		updateRecord as updateRecordTrait;
	}

	protected $table = 'rangers';

	protected $allowedFields = ['slug', 'name', 'description', 'photo', 'morpherId'];

	protected $nestedModelClasses = [
		'morpherModel' => 'App\Models\MorpherModel'
	];

	protected $validationRules = [
		'slug' => 'required_with[name]|max_length[100]',
		'name' => 'required|max_length[100]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[100]',
		'morpherId' => 'permit_empty|is_natural_no_zero|exists_id[morphers.id]'
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
		$castingModel = model('App\Models\CastingModel');
		if ($castingModel->countByRangerId($id)) {
			$errors['casting'] = 'There are nested casting records to ranger';
		}
		$seasonArsenalModel = model('App\Models\SeasonArsenalModel');
		if ($seasonArsenalModel->countByRangerId($id)) {
			$errors['season_arsenal'] = 'There are nested season-arsenal records to ranger';
		}
		$seasonZordModel = model('App\Models\SeasonZordModel');
		if ($seasonZordModel->countByRangerId($id)) {
			$errors['season_zord'] = 'There are nested season-zord records to ranger';
		}
		$transformationRangerModel = model('App\Models\TransformationRangerModel');
		if ($transformationRangerModel->countByRangerId($id)) {
			$errors['transformation_ranger'] = 'There are nested transformation-ranger records to ranger';
		}
	}

	public function insertRecord(&$record, $subTransaction = false)
	{
		if (!$subTransaction) {
			$this->db->transBegin();
		}

		if (isset($record['morpher'])) {
			$morpherModel = model($this->nestedModelClasses['morpherModel']);

			$morpherResult = $morpherModel->insertRecord($record['morpher'], true);
			if ($morpherResult !== true) {
				$this->db->transRollback();
				return $morpherResult;
			}

			$record['morpherId'] = $record['morpher']['id'];
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

	public function updateRecord($record, $id)
	{
		$this->db->transBegin();

		if (isset($record['morpher'])) {
			$morpherModel = model($this->nestedModelClasses['morpherModel']);

			$morpherResult = $morpherModel->insertRecord($record['morpher']);
			if ($morpherResult !== true) {
				$this->db->transRollback();
				return $morpherResult;
			}

			$record['morpherId'] = $record['morpher']['id'];
		}

		// Se actualiza los datos del ranger
		$result = $this->updateRecordTrait($record, $id);
		if ($result !== true) {
			$this->db->transRollback();
			return $this->errors();
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

		// Se valida los datos del Morpher
		if (isset($postData['morpher'])) {
			// Se elimina el Id del morpher y los Ids de rangers a asociar al morpher (Si aplica)
			unset($postData['morpherId']);
			unset($postData['morpher']['rangersId']);

			$morpherModel = model($this->nestedModelClasses['morpherModel']);
			$morpherErrors = $morpherModel->validateRecord($postData['morpher'], isset($postFiles['morpher']) ? $postFiles['morpher'] : [], 'post');
			if ($morpherErrors !== true) {
				$errors['morpher'] = $morpherErrors;
			}
		}

		return empty($errors) ? true : $errors;
	}
}
