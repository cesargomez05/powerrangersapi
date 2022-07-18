<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CastingFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($serieId, $seasonNumber, $actorId = null, $characterId = null, $rangerId = null)
	{
		// Valida la existencia de la temporada asociada la consulta
		$validation = SeasonFilter::checkRecord($serieId, $seasonNumber, 'Season');
		if (isset($validation)) {
			return $validation;
		}

		$model = model('App\Models\CastingModel');
		$model->setPublic(self::isPublic());

		// Se realiza la validación de los parámetros correspondientes al casting
		$validateCastingParameters = self::validateCastingParameters($actorId, $characterId, $rangerId);
		if (isset($validateCastingParameters)) {
			return $validateCastingParameters;
		}

		// Se valida la existencia de registro del casting
		$exists = $model->check($serieId, $seasonNumber, $actorId, $characterId, $rangerId);
		if (!$exists) {
			return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Casting not found');
		}
	}

	private static function validateCastingParameters($actorId, $characterId, $rangerId)
	{
		if (isset($actorId) && isset($characterId)) {
			$validation = ActorFilter::checkRecord($actorId, 'Actor');
			if (isset($validation)) {
				return $validation;
			}

			$validation = CharacterFilter::checkRecord($characterId, 'Character');
			if (isset($validation)) {
				return $validation;
			}

			if (isset($rangerId)) {
				$validation = RangerFilter::checkRecord($rangerId, 'Ranger');
				if (isset($validation)) {
					return $validation;
				}
			}
		}
	}
}
