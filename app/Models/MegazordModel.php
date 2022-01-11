<?php

namespace App\Models;

class MegazordModel extends APIModel
{
	// Atributos de la clase APIModel
	public $photoField = 'photo';
	protected $validationRulesCreate = [
		'zordsId' => 'permit_empty|check_comma_separated|validate_children_ids[zords.id]'
	];

	// Atributos de la clase Model
	protected $table = 'megazords';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['slug', 'name', 'description', 'photo'];
	protected $validationRules = [
		'slug' => 'required|max_length[100]',
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

	public function validateRecord(&$filesData, $property, &$postData, $postFiles, $ids, $method, $record = null, $nodes = [])
	{
		// Se valida si el proceso corresponde a un nuevo registro de serie
		if ($method == 'post') {
			// Se valida si existe la propiedad donde se establece los datos de la relación SeasonMegazord
			if (!isset($postData['seasonmegazord'])) {
				return ['seasonmegazord' => 'Please set the season-megazord relation values'];
			}

			// Se invoca el método que valida los datos de la relación SeasonZord mediante su respectiva clase Model.
			$seasonMegazordModel = new SeasonMegazordModel();
			// Se omite la regla de validación correspondiente al Id del Megazord que se va a crear
			$seasonMegazordModel->removeValidationRule('megazordId');
			$validRecord = $seasonMegazordModel->validateRecord($filesData, 'seasonmegazord', $postData['seasonmegazord'], $postFiles, [], 'post', null, array_merge($nodes, ['seasonmegazord']));
			if ($validRecord !== true) {
				return ['seasonmegazord' => $validRecord];
			}
		}
		return parent::validateRecord($filesData, $property, $postData, $postFiles, $ids, $method, $record, $nodes);
	}
}
