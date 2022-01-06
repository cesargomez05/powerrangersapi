<?php

namespace App\Models;

class ChapterModel extends APIModel
{
	// Atributos de la clase APIModel
	protected $primaryKeys = ['serieId', 'seasonNumber', 'number'];
	protected $filterColumns = ['title', 'titleSpanish'];
	protected $columnValue = 'title';

	protected $viewName = "chapters_view";
	protected $uriColumns = ['serieSlug', 'seasonNumber', 'slug'];
	protected $viewColumns = ['slug', 'title'];

	// Atributos de la clase Model
	protected $table = 'chapters';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['serieId', 'seasonNumber', 'number', 'slug', 'title', 'titleSpanish', 'summary'];
	protected $validationRules = [
		'serieId' => 'required|is_natural_no_zero|exists_id[series.id]',
		'seasonNumber' => 'required|is_natural_no_zero',
		'number' => 'required|is_natural_no_zero',
		'slug' => 'required|max_length[100]',
		'title' => 'required|max_length[100]',
		'titleSpanish' => 'required|max_length[100]',
		'summary' => 'permit_empty',
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
