<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class TransformationRangerModel extends Model
{
	use ModelTrait {
		list as public listTrait;
	}

	protected $table = 'transformation_ranger';

	protected $allowedFields = ['transformationId', 'rangerId', 'name', 'photo'];

	protected $validationRules = [
		'transformationId' => 'required|is_natural_no_zero|exists_id[actors.id]',
		'rangerId' => 'required|is_natural_no_zero|exists_id[characters.id]',
		'name' => 'permit_empty|max_length[100]',
		'photo' => 'permit_empty|max_length[100]'
	];

	public function validateRecord(&$postData, $postFiles, $method, $prevRecord = null)
	{
		$errors = $this->validateUploadFiles($postData, $postFiles);
		if ($errors === true) {
			$errors = [];
		}

		$this->validateRecordProperties($postData, $method, $prevRecord);

		if (!$this->validate($postData)) {
			$errors = array_merge($this->errors(), $errors);
		}

		return count($errors) > 0 ? $errors : true;
	}

	public function insertTransformationRangers($transformationId, $rangers)
	{
		$this->setValidationRule('transformationId', 'permit_empty');

		foreach ($rangers as &$ranger) {
			$ranger['transformationId'] = $transformationId;
		}

		return $this->insertBatch($rangers);
	}
}
