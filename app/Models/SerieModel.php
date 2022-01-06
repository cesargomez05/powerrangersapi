<?php

namespace App\Models;

class SerieModel extends APIModel
{
	// Atributos de la clase APIModel
	protected $filterColumns = ['title'];
	protected $columnValue = '';
	protected $viewColumns = ['slug', 'title'];

	// Atributos de la clase Model
	protected $table = 'series';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['slug', 'title'];
	protected $validationRules = [
		'slug' => 'required|max_length[50]|is_unique[series.slug,id,{_id}]',
		'title' => 'required|max_length[50]'
	];

	public function validateRecord(&$filesData, $property, &$postData, $postFiles, $ids, $method, $record = null, $nodes = [])
	{
		// Se valida si el proceso corresponde a un nuevo registro de serie
		if ($method == 'post') {
			// Se valida la propiedad de los datos de la temporada
			if (!isset($postData['season'])) {
				return ['season' => 'Please set the season values'];
			}

			// Se valida los datos de la temporada
			$seasonModel = new SeasonModel();
			// Se elimina las validaciones del Id asociadas a la temporada
			$seasonModel->removeValidationRule('serieId,number');
			$validRecord = $seasonModel->validateRecord($filesData, 'season', $postData['season'], $postFiles, [], 'post', null, array_merge($nodes, ['season']));
			if ($validRecord !== TRUE) {
				return ['season' => $validRecord];
			}
		}
		return parent::validateRecord($filesData, $property, $postData, $postFiles, $ids, $method, $record, $nodes);
	}
}
