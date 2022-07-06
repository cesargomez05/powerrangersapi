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

	protected $allowedFields = ['slug', 'name', 'description', 'photo'];

	protected $nestedModelClasses = [
		'morpherModel' => 'App\Models\MorpherModel',
		'rangerMorpherModel' => 'App\Models\RangerMorpherModel'
	];

	protected $validationRules = [
		'slug' => 'required_with[name]|max_length[100]',
		'name' => 'required|max_length[100]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[25]'
	];

	protected $returnType = \App\Entities\Ranger::class;

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
		$castingModel = model('App\Models\CastingModel');
		if ($castingModel->countByRangerId($id)) {
			$errors['casting'] = 'There are nested casting records to ranger';
		}
		$rangerMorpherModel = model('App\Models\RangerMorpherModel');
		if ($rangerMorpherModel->countByRangerId($id)) {
			$errors['ranger_morpher'] = 'There are nested ranger-morpher records to ranger';
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

	protected function addRecordAttributes($ranger, $slug)
	{
		$rangerMorpherModel = model('App\Models\RangerMorpherModel');
		$ranger->morpher = $rangerMorpherModel->getByRanger($slug);
	}

	public function insertRecord(&$record, $subTransaction = false)
	{
		if (!$subTransaction) {
			$this->db->transBegin();
		}

		// Se procede a insertar el registro en la base de datos
		$result = $this->insertRecordTrait($record);
		if ($result !== true) {
			$this->db->transRollback();
			return $result;
		}

		$rangerMorpherModel = model($this->nestedModelClasses['rangerMorpherModel']);
		$rangerMorpherModel->setValidationRule('rangerId', 'permit_empty');

		// Se valida si se seleccionó un morpher previamente creado a asociar al ranger
		if (isset($record['morpherId'])) {
			// Se valida los datos del morpher a asociar al ranger
			if (!isset($record['morpher'])) {
				$record['morpher'] = [];
			}
			// Se establece el Id del ranger creado en la asociación con el Morpher
			$record['morpher']['rangerId'] = $record['id'];
			$record['morpher']['morpherId'] = $record['morpherId'];

			// Se registra los datos de la asociación entre el ranger y el morpher
			$rangerMorpherResult = $rangerMorpherModel->insertRecord($record['morpher'], true);
			if ($rangerMorpherResult !== true) {
				$this->db->transRollback();
				return $rangerMorpherResult;
			}
		} else {
			if (isset($record['morpher'])) {
				// Se elimina la lista de Id de rangers
				unset($record['morpher']['rangersId']);

				// Se inserta los datos del morpher
				$morpherModel = model($this->nestedModelClasses['morpherModel']);
				$morpherResult = $morpherModel->insertRecord($record['morpher'], true);
				if ($morpherResult !== true) {
					$this->db->transRollback();
					return $morpherResult;
				}

				// Se registra los datos de la asociación entre el ranger y el morpher
				$rangerMorpherRecord = ['rangerId' => $record['id'], 'morpherId' => $record['morpher']['id']];
				$rangerMorpherResult = $rangerMorpherModel->insertRecord($rangerMorpherRecord, true);
				if ($rangerMorpherResult !== true) {
					$this->db->transRollback();
					return $rangerMorpherResult;
				}
			}
		}

		if (!$subTransaction) {
			$this->db->transCommit();
		}

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

		// Se valida si se definió el Id de un morpher previamente creado
		if (isset($postData['morpherId'])) {
			// Se valida el valor del Id del morpher
			$morpherModel = model($this->nestedModelClasses['morpherModel']);
			$validationId = $morpherModel->validateId($postData['morpherId'], 'morpherId', 'Morpher id');
			if ($validationId !== true) {
				$errors = array_merge($errors, $validationId);
			} else {
				// Se valida la existencia del morpher
				$existsMorpher = $morpherModel->check($postData['morpherId']);
				if (!$existsMorpher) {
					$errors['morpherId'] = 'Morpher not found';
				} else {
					// Se valida los datos del morpher a asociar al ranger
					if (!isset($postData['morpher'])) {
						$postData['morpher'] = [];
					}
					$postData['morpher']['morpherId'] = $postData['morpherId'];

					$rangerMorpherModel = model($this->nestedModelClasses['rangerMorpherModel']);
					$rangerMorpherModel->setValidationRule('rangerId', 'permit_empty');
					unset($postData['morpher']['rangerId']);

					$rangerMorpherErrors = $rangerMorpherModel->validateRecord($postData['morpher'], isset($postFiles['morpher']) ? $postFiles['morpher'] : [], 'post');
					if ($rangerMorpherErrors !== true) {
						$errors['morpher'] = $rangerMorpherErrors;
					}
				}
			}
		} else {
			if (isset($postData['morpher'])) {
				$morpherModel = model($this->nestedModelClasses['morpherModel']);
				$morpherErrors = $morpherModel->validateRecord($postData['morpher'], isset($postFiles['morpher']) ? $postFiles['morpher'] : [], 'post');
				if ($morpherErrors !== true) {
					$errors['morpher'] = $morpherErrors;
				}
			}
		}

		return empty($errors) ? true : $errors;
	}
}
