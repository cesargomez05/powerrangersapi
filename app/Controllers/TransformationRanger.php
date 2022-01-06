<?php

namespace App\Controllers;

class TransformationRanger extends APIController
{
	// Atributos de la clase APIController
	protected $existsRecordMessage = 'The record information is used by other record in this season';
	protected $parentRecordNotFoundMessage = 'Transformation id not found';

	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\TransformationRangerModel';
}
