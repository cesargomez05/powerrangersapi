<?php

namespace App\Models;

class MorpherModel extends APIModel
{
	// Atributos de la clase APIModel
	protected $photoField = 'photo';
	protected $columnValue = '';
	protected $validationRulesCreate = [
		'rangersId' => 'required|check_comma_separated|validate_children_ids[rangers.id]'
	];

	// Atributos de la clase Model
	protected $table = 'morphers';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['name', 'description', 'photo'];
	protected $validationRules = [
		'name' => 'required|max_length[100]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[25]'
	];
	protected $validationMessages = [
		'rangersId' => [
			'check_comma_separated' => 'Please set the rangers id by comma separated',
			'validate_children_ids' => 'The rangers ids not exist or are invalid'
		]
	];

	public function validateRecord(&$filesData, $property, &$postData, $postFiles, $ids, $method, $record = null, $nodes = [])
	{
		// Se valida si el llamado proviene para un registro anidado
		if (in_array('morpher', $nodes)) {
			// Se elimina la propiedad correspondiente al Id de los Rangers; así como la validación de la misma
			unset($postData['rangersId']);
			$this->removeValidationRule('rangersId');
		}
		return parent::validateRecord($filesData, $property, $postData, $postFiles, $ids, $method, $record, $nodes);
	}
}
