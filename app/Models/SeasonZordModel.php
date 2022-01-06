<?php

namespace App\Models;

class SeasonZordModel extends APIModel
{
	// Atributos de la clase APIModel
	protected $primaryKeys = ['serieId', 'seasonNumber', 'zordId'];
	protected $filterColumns = [];
	protected $columnValue = '';

	// Atributos de la clase Model
	protected $table = 'season_zord';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['serieId', 'seasonNumber', 'zordId', 'rangerId'];
	protected $validationRules = [
		'serieId' => 'required|is_natural_no_zero|exists_id[series.id]',
		'seasonNumber' => 'required|is_natural_no_zero',
		'zordId' => 'required|is_natural_no_zero|exists_id[zords.id]',
		'rangerId' => 'permit_empty|is_natural_no_zero|exists_id[rangers.id]',
		'seasonId' => 'check_id[seasonId,serieId,seasonNumber]|exists_record[seasonId,seasons,serieId,number]'
	];
	protected $validationMessages = [
		'seasonId' => [
			'check_id' => 'The \'serieId\' and \'seasonNumber\' values are required',
			'exists_record' => 'The season not exists'
		]
	];

	public function insertRecord(&$record)
	{
		// Se elimina la propiedad del Id de la temporada
		unset($record['seasonId']);
		return parent::insertRecord($record);
	}
}
