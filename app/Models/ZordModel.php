<?php

namespace App\Models;

class ZordModel extends APIModel
{
	// Atributos de la clase APIModel
	public $photoField = 'photo';

	// Atributos de la clase Model
	protected $table = 'zords';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['slug', 'name', 'description', 'photo'];
	protected $validationRules = [
		'slug' => 'required|max_length[100]',
		'name' => 'required|max_length[100]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[100]'
	];

	public function validateRecord(&$filesData, $property, &$postData, $postFiles, $ids, $method, $record = null, $nodes = [])
	{
		$errors = [];
		// Se valida si el proceso corresponde a un nuevo registro de serie
		if ($method == 'post') {
			// Se valida si existe la propiedad donde se establece los datos de la relación SeasonZord
			if (!isset($postData['seasonzord'])) {
				return ['seasonzord' => 'Please set the season-zord relation values'];
			}

			// Se invoca el método que valida los datos de la relación SeasonZord mediante su respectiva clase Model.
			$seasonZordModel = new seasonZordModel();
			// Se omite la regla de validación correspondiente al Id del Zord que se va a crear
			$seasonZordModel->removeValidationRule('zordId');
			$validRecord = $seasonZordModel->validateRecord($filesData, 'seasonzord', $postData['seasonzord'], $postFiles, [], 'post', null, array_merge($nodes, ['seasonzord']));
			if ($validRecord !== TRUE) {
				return ['seasonzord' => $validRecord];
			}
		}
		return count($errors) ? $errors : parent::validateRecord($filesData, $property, $postData, $postFiles, $ids, $method, $record, $nodes);
	}
}
