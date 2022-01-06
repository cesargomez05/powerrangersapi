<?php

namespace App\Models;

class SeasonModel extends APIModel
{
	// Atributos de la clase APIModel
	protected $primaryKeys = ['serieId', 'number'];
	protected $filterColumns = [];
	protected $columnValue = '';
	protected $uriColumn = '';

	protected $viewName = "seasons_view";
	protected $uriColumns = ['serieSlug', 'number'];
	protected $viewColumns = ['number', 'year'];

	// Atributos de la clase Model
	protected $table = 'seasons';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['serieId', 'number', 'year', 'title', 'ageId', 'synopsis'];
	protected $validationRules = [
		'serieId' => 'required|is_natural_no_zero|exists_id[series.id]',
		'number' => 'required|is_natural_no_zero',
		'year' => 'permit_empty|is_year',
		'title' => 'permit_empty|max_length[50]',
		'synopsis' => 'permit_empty',
		'ageId' => 'required|is_natural_no_zero|exists_id[ages.id]'
	];

	public function validateRecord(&$filesData, $property, &$postData, $postFiles, $ids, $method, $record = null, $nodes = [])
	{
		$errors = [];
		$this->validateNestedRecord($errors, $filesData, $postData, $postFiles, 'age', 'AgeModel', 'ageId', array_merge($nodes, ['age']));
		return count($errors) ? $errors : parent::validateRecord($filesData, $property, $postData, $postFiles, $ids, $method, $record, $nodes);
	}

	public function insertNestedRecords(&$season)
	{
		$errors = [];
		$this->insertNestedRecord($errors, $season, 'age', 'AgeModel', 'ageId');
		return count($errors) ? $errors : TRUE;
	}

	public function getSeasonsByAge($ageUri)
	{
		// Se define la sentencia para los registros de la vista
		$this->setViewConditions();
		$this->where('ageSlug', $ageUri);

		// Se ejecuta la consulta y se retorna el resultado
		$response = $this->get()->getResultArray();
		return $response;
	}

	public function getSeasonsBySerie($serieUri)
	{
		// Se define la sentencia para los registros de la vista
		$this->setViewConditions(['number', 'year']);
		$this->where('serieSlug', $serieUri);

		// Se ejecuta la consulta y se retorna el resultado
		$response = $this->get()->getResultArray();
		return $response;
	}
}
