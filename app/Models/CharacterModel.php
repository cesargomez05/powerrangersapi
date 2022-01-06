<?php

namespace App\Models;

class CharacterModel extends APIModel
{
	// Atributos de la clase APIModel
	public $photoField = 'photo';

	// Atributos de la clase Model
	protected $table = 'characters';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['slug', 'name', 'fullName', 'description', 'photo'];
	protected $validationRules = [
		'slug' => 'required|max_length[50]',
		'name' => 'required|max_length[50]',
		'fullName' => 'permit_empty|max_length[150]',
		'description' => 'permit_empty',
		'photo' => 'permit_empty|max_length[100]'
	];
}
