<?php

namespace App\Models;

class TransformationRangerModel extends APIModel
{
	// Atributos de la clase APIModel
	public $photoField = 'photo';
	protected $columnValue = '';
	protected $primaryKeys = ['transformationId', 'rangerId'];

	// Atributos de la clase Model
	protected $table = 'transformation_ranger';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['transformationId', 'rangerId', 'name', 'photo'];
	protected $validationRules = [
		'transformationId' => 'required|is_natural_no_zero|exists_id[actors.id]',
		'rangerId' => 'required|is_natural_no_zero|exists_id[characters.id]',
		'name' => 'permit_empty|max_length[100]',
		'photo' => 'permit_empty|max_length[100]'
	];
}
