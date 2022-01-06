<?php

namespace App\Models;

class VillainModel extends APIModel
{
	// Atributos de la clase APIModel
	public $photoField = 'photo';

	// Atributos de la clase Model
	protected $table = 'villains';

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
		// Se valida si el proceso corresponde a un nuevo registro de villano
		if ($method == 'post') {
			// Se valida si existe la propiedad donde se establece los datos de la relación SeasonVillain
			if (!isset($postData['seasonvillain'])) {
				return ['seasonvillain' => 'Please set the season-villain relation values'];
			}

			// Se invoca el método que valida los datos de la relación SeasonVillain mediante su respectiva clase Model.
			$seasonVillainModel = new SeasonVillainModel();
			// Se omite la regla de validación correspondiente al Id del Villano que se va a crear
			$seasonVillainModel->removeValidationRule('villainId');
			$validRecord = $seasonVillainModel->validateRecord($filesData, 'seasonvillain', $postData['seasonvillain'], $postFiles, [], 'post', null, array_merge($nodes, ['seasonvillain']));
			if ($validRecord !== TRUE) {
				return ['seasonvillain' => $validRecord];
			}
		}
		return parent::validateRecord($filesData, $property, $postData, $postFiles, $ids, $method, $record, $nodes);
	}
}
