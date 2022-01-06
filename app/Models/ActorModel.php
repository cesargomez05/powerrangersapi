<?php

namespace App\Models;

class ActorModel extends APIModel
{
	// Atributos de la clase APIModel
	public $photoField = 'photo';

	// Atributos de la clase Model
	protected $table = 'actors';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['slug', 'name', 'birthDate', 'deathDate', 'photo'];
	protected $validationRules = [
		'slug' => 'required|max_length[50]',
		'name' => 'required|max_length[50]',
		'birthDate' => 'permit_empty|valid_date[Y-m-d]',
		'deathDate' => 'permit_empty|valid_date[Y-m-d]',
		'photo' => 'permit_empty|max_length[100]'
	];
}
