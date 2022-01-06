<?php

namespace App\Controllers;

use App\Models\SeasonModel;

class Casting extends APIController
{
	// Atributos de la clase BaseResource
	protected $modelName = 'App\Models\CastingModel';

	// Atributos de la clase APIController
	protected $existsRecordMessage = 'The casting information is used by other record in this season';
	protected $parentRecordNotFoundMessage = 'Season id not found';

	/**
	 * Ejecuta el proceso de actualización del registro.
	 * Dado que el quinto parámetro ($rangerId) puede no definirse en la ruta (ver archivo Routes.php)
	 * se debe indicar todos los parámetros en el método e invocar el mismo asociado en la clase padre.
	 */
	public function update($serieId = null, $seasonNumber = null, $actorId = null, $characterId = null, $rangerId = null)
	{
		return parent::update($serieId, $seasonNumber, $actorId, $characterId, $rangerId);
	}

	/**
	 * Ejecuta el proceso de eliminar el registro.
	 * Dado que el quinto parámetro ($rangerId) puede no definirse en la ruta (ver archivo Routes.php)
	 * se debe indicar todos los parámetros en el método e invocar el mismo asociado en la clase padre.
	 */
	public function delete($serieId = null, $seasonNumber = null, $actorId = null, $characterId = null, $rangerId = null)
	{
		return parent::delete($serieId, $seasonNumber, $actorId, $characterId, $rangerId);
	}

	public function getList($serieSlug = null, $seasonNumber = null, $isTeamUp = null)
	{
		// Se obtiene los parámetros de consulta de registros
		$filter = $this->request->getGet();
		set_pagination($filter);

		// Se ejecuta la consulta de registros
		$records = $this->model->getList($filter, [$serieSlug, $seasonNumber], $isTeamUp);
		return $this->respond($records);
	}

	protected function checkParentRecord($ids, $isUpdate = FALSE)
	{
		// Se valida los datos de la temporada
		$seasonModel = new SeasonModel();
		$season = $seasonModel->getRecord($isUpdate ? array_slice($ids, 0, 2) : $ids);
		return (bool) $season !== FALSE;
	}
}
