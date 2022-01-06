<?php

namespace App\Models;

class AgeModel extends APIModel
{
	// Atributos de la clase Model
	protected $table = 'ages';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['slug', 'name'];
	protected $validationRules = [
		'slug' => 'required|max_length[20]',
		'name' => 'required|max_length[20]'
	];
}
