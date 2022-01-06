<?php

namespace App\Controllers;

class MegazordZord extends APIController
{
	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\MegazordZordModel';

	// Atributos de la clase APIController
	protected $existsRecordMessage = 'The record information is used by other record in this season';
	protected $parentRecordNotFoundMessage = 'Season id not found';
}
