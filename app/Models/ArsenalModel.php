<?php

namespace App\Models;

class ArsenalModel extends APIModel
{
	// Atributos de la clase APIModel
	public $photoField = 'photo';

	// Atributos de la clase Model
	protected $table = 'arsenal';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['slug', 'name', 'description', 'photo'];
	protected $validationRules = [
		'slug' => 'required|max_length[100]',
		'name' => 'required|max_length[100]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[25]'
	];

	public function validateRecord(&$filesData, $property, &$postData, $postFiles, $ids, $method, $record = null, $nodes = [])
	{
		$errors = [];
		// Se valida si el proceso corresponde a un nuevo registro de serie
		if ($method == 'post') {
			// Se valida si existe la propiedad donde se establece los datos de la relación SeasonArsenal
			if (!isset($postData['seasonarsenal'])) {
				return ['seasonarsenal' => 'Please set the season-arsenal relation values'];
			}

			// Se invoca el método que valida los datos de la relación SeasonArsenal mediante su respectiva clase Model.
			$seasonArsenalModel = new SeasonArsenalModel();
			// Se omite la regla de validación correspondiente al Id del Arsenal que se va a crear
			$seasonArsenalModel->removeValidationRule('arsenalId');
			$validRecord = $seasonArsenalModel->validateRecord($filesData, 'seasonarsenal', $postData['seasonarsenal'], $postFiles, [], 'post', null, array_merge($nodes, ['seasonarsenal']));
			if ($validRecord !== true) {
				return ['seasonarsenal' => $validRecord];
			}
		}
		return count($errors) ? $errors : parent::validateRecord($filesData, $property, $postData, $postFiles, $ids, $method, $record, $nodes);
	}
}
