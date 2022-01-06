<?php

namespace App\Models;

class RangerModel extends APIModel
{
	// Atributos de la clase APIModel
	protected $photoField = 'photo';

	// Atributos de la clase Model
	protected $table = 'rangers';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['slug', 'name', 'description', 'photo', 'morpherId'];
	protected $validationRules = [
		'slug' => 'required|max_length[100]',
		'name' => 'required|max_length[100]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[100]',
		'morpherId' => 'permit_empty|is_natural_no_zero|exists_id[morphers.id]'
	];

	public function validateRecord(&$filesData, $property, &$postData, $postFiles, $ids, $method, $record = null, $nodes = [])
	{
		$errors = [];
		$this->validateNestedRecord($errors, $filesData, $postData, $postFiles, 'morpher', 'MorpherModel', 'morpherId', array_merge($nodes, ['morpher']));
		return count($errors) ? $errors : parent::validateRecord($filesData, $property, $postData, $postFiles, $ids, $method, $record, $nodes);
	}

	public function insertNestedRecords(&$ranger)
	{
		$errors = [];
		$this->insertNestedRecord($errors, $ranger, 'morpher', 'MorpherModel', 'morpherId');
		return count($errors) ? $errors : TRUE;
	}

	public function setRangersMorpher($rangersId, $morpherId)
	{
		$response = [];

		// Se ejecuta la sentencia UPDATE para establecer el Id del morpher, a cada uno de los rangers
		$result = $this->update(explode(',', $rangersId), ['morpherId' => $morpherId]);
		if ($result === FALSE) {
			$response['error'] = $this->errors();
			return $response;
		}

		return $response;
	}
}
