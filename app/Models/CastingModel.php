<?php

namespace App\Models;

class CastingModel extends APIModel
{
	// Atributos de la clase APIModel
	protected $primaryKeys = ['serieId', 'seasonNumber', 'actorId', 'characterId', 'rangerId'];
	protected $filterColumns = ['actorName', 'characterName'];
	protected $columnValue = '';

	protected $viewName = "casting_view";
	protected $uriColumns = ['serieSlug', 'seasonNumber'];
	protected $viewColumns = ['actorSlug', 'actorName', 'characterSlug', 'characterName', 'rangerSlug', 'rangerName'];

	// Atributos de la clase Model
	protected $table = 'casting';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['serieId', 'seasonNumber', 'actorId', 'characterId', 'rangerId', 'isTeamUp'];
	protected $validationRules = [
		'serieId' => 'required|is_natural_no_zero|exists_id[series.id]',
		'seasonNumber' => 'required|is_natural_no_zero',
		'actorId' => 'required|is_natural_no_zero|exists_id[actors.id]',
		'characterId' => 'required|is_natural_no_zero|exists_id[characters.id]',
		'rangerId' => 'permit_empty|is_natural_no_zero|exists_id[rangers.id]',
		'isTeamUp' => 'required|is_natural|in_list[0,1]',
		'seasonId' => 'check_id[seasonId,serieId,seasonNumber]|exists_record[seasonId,seasons,serieId,number]'
	];
	protected $validationMessages = [
		'seasonId' => [
			'check_id' => 'The \'serieId\' and \'seasonNumber\' values are required',
			'exists_record' => 'The season not exists'
		]
	];

	public function getRecordsByFilter($filter, $ids = null)
	{
		// Se cambia la tabla sobre la cual se realiza la consulta de registros
		$this->setTable('view_casting');
		return parent::getRecordsByFilter($filter, $ids);
	}

	public function validateRecord(&$filesData, $property, &$postData, $postFiles, $ids, $method, $record = null, $nodes = [])
	{
		$errors = [];
		$this->validateNestedRecord($errors, $filesData, $postData, $postFiles, 'actor', 'ActorModel', 'actorId', array_merge($nodes, ['actor']));
		$this->validateNestedRecord($errors, $filesData, $postData, $postFiles, 'character', 'CharacterModel', 'characterId', array_merge($nodes, ['character']));
		$this->validateNestedRecord($errors, $filesData, $postData, $postFiles, 'ranger', 'RangerModel', 'rangerId', array_merge($nodes, ['ranger']));
		return count($errors) ? $errors : parent::validateRecord($filesData, $property, $postData, $postFiles, $ids, $method, $record, $nodes);
	}

	public function insertRecord(&$record)
	{
		// Se elimina la propiedad del Id de la temporada
		unset($record['seasonId']);
		return parent::insertRecord($record);
	}

	public function insertNestedRecords(&$casting)
	{
		$errors = [];
		$this->insertNestedRecord($errors, $casting, 'actor', 'ActorModel', 'actorId');
		$this->insertNestedRecord($errors, $casting, 'character', 'CharacterModel', 'characterId');
		$this->insertNestedRecord($errors, $casting, 'ranger', 'RangerModel', 'rangerId');
		return count($errors) ? $errors : TRUE;
	}

	public function getList($filter, $uris = null, $isTeamUp = null)
	{
		$response = [];

		// Se define la sentencia para los registros de la vista
		$this->setViewConditions();

		// Se define en la sentencia where de la consulta las URIS del registro
		$this->setConditionURIs($uris);
		$this->where('isTeamUp', $isTeamUp);

		// Se ejecuta la consulta de los registros
		$this->getRecords($filter, $response);

		return $response;
	}

	public function getCastingByActor($actorUri)
	{
		$this->setViewConditions(['serieSlug', 'serieTitle', 'seasonNumber', 'characterSlug', 'characterName', 'rangerSlug', 'rangerName', 'isTeamUp']);
		$this->where('actorSlug', $actorUri);

		$response = $this->get()->getResultArray();
		return $response;
	}

	public function getCastingByCharacter($characterUri)
	{
		$this->setViewConditions(['serieSlug', 'serieTitle', 'seasonNumber', 'actorSlug', 'actorName', 'rangerSlug', 'rangerName', 'isTeamUp']);
		$this->where('characterSlug', $characterUri);

		$response = $this->get()->getResultArray();
		return $response;
	}

	public function getCastingByRanger($rangerUri)
	{
		$this->setViewConditions(['serieSlug', 'serieTitle', 'seasonNumber', 'actorSlug', 'actorName', 'characterSlug', 'characterName', 'isTeamUp']);
		$this->where('rangerSlug', $rangerUri);

		$response = $this->get()->getResultArray();
		return $response;
	}
}
