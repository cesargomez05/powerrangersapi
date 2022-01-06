<?php

namespace App\Models;

class MegazordZordModel extends APIModel
{
	// Atributos de la clase APIModel
	protected $primaryKeys = ['megazordId', 'zordId'];
	protected $filterColumns = [];
	protected $columnValue = '';

	// Atributos de la clase Model
	protected $table = 'megazord_zord';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['megazordId', 'zordId'];
	protected $validationRules = [
		'megazordId' => 'required|is_natural_no_zero|exists_id[zords.id]',
		'zordId' => 'required|is_natural_no_zero|exists_id[zords.id]'
	];
}
