<?php

namespace App\Models;

class ModuleModel extends APIModel
{
	// Atributos de la clase APIModel
	protected $columnValue = '';

	// Atributos de la clase Model
	protected $table = 'modules';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['id', 'name'];
	protected $validationRules = [
		'id' => 'required|max_length[50]|is_unique[modules.id,id,{_id}]',
		'name' => 'required|max_length[50]'
	];
}
