<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class TransformationModel extends Model
{
	use ModelTrait {
		insertRecord as insertRecordTrait;
	}

	protected $table = 'transformations';

	protected $allowedFields = ['slug', 'name', 'description'];

	protected $nestedModelClasses = [
		'transformationRangerModel' => 'App\Models\TransformationRangerModel'
	];

	protected $validationRules = [
		'slug' => 'required_with[name]|max_length[100]',
		'name' => 'required|max_length[100]',
		'description' => 'permit_empty'
	];

	protected $returnType = \App\Entities\Transformation::class;

	protected function setRecordsCondition($query)
	{
		$this->select(['id URI', 'name']);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('name', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setRecordCondition($id)
	{
		$this->select(['*']);
		$this->where('id', $id);
	}

	protected function checkNestedRecords($id, &$errors)
	{
		$transformationRangerModel = model($this->nestedModelClasses['transformationRangerModel']);
		if ($transformationRangerModel->countByTransformationId($id)) {
			$errors['transformation_ranger'] = 'There are nested transformation-ranger records to transformation';
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

		if (isset($record['rangers']) && count($record['rangers']) > 0) {
			$transformationRangerModel = model($this->nestedModelClasses['transformationRangerModel']);
			$transformationRangerResult = $transformationRangerModel->insertTransformationRangers($record[$this->primaryKey], $record['rangers']);
			if ($transformationRangerResult === false) {
				$this->db->transRollback();
				return $transformationRangerModel->errors();
			}
		}

		$this->db->transCommit();

		return true;
	}

	protected function setPublicRecordsCondition($query)
	{
		$this->select(['slug URI', 'name']);
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('name', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	protected function setPublicRecordCondition($slug)
	{
		$this->select(['name', 'description']);
		$this->where('slug', $slug);
	}

	public function validateRecord(&$postData, $postFiles, $method, $prevRecord = null)
	{
		$errors = [];

		$this->validateRecordProperties($postData, $method, $prevRecord);

		$slugSettings = ['title' => 'name', 'field' => 'slug', 'id' => [$this->primaryKey]];
		$this->setSlugValue($postData, $slugSettings, isset($prevRecord) ? [$prevRecord[$this->primaryKey]] : null);

		// Se valida los datos de la temporada (si es método POST)
		if ($method == 'post') {
			if (!isset($postData['rangers'])) {
				$postData['rangers'] = [];
			}
			if (!isset($postFiles['rangers'])) {
				$postFiles['rangers'] = [];
			}

			$rangersErrors = [];
			$rangersId = [];

			$transformationRangerModel = model($this->nestedModelClasses['transformationRangerModel']);
			$transformationRangerModel->setValidationRule('transformationId', 'permit_empty');
			unset($postData['transformationId']);

			// Número de registros correspondientes a los rangers
			$count = max(count($postData['rangers']), count($postFiles['rangers']));
			for ($i = 0; $i < $count; $i++) {
				if (!isset($postData['rangers'][$i])) {
					$postData['rangers'][$i] = [];
				}
				if (!isset($postFiles['rangers'][$i])) {
					$postFiles['rangers'][$i] = [];
				}

				$transformationRangerErrors = $transformationRangerModel->validateRecord($postData['rangers'][$i], $postFiles['rangers'][$i], 'post');
				if ($transformationRangerErrors !== true) {
					$rangersErrors[$i] = $transformationRangerErrors;
				} else {
					$rangerId = $postData['rangers'][$i]['rangerId'];
					if (in_array($rangerId, $rangersId)) {
						$rangersErrors[$i] = ['rangerId' => 'The ranger id is used by other record'];
					} else {
						$rangersId[] = $rangerId;
					}
				}
			}

			if (!empty($rangersErrors)) {
				$errors['rangers'] = $rangersErrors;
			}
		} else {
			unset($postData['rangers']);
		}

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return empty($errors) ? true : $errors;
	}
}
