<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class CastingFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($serieId, $seasonNumber, $actorId = null, $characterId = null, $rangerId = null)
	{
		$validation = SeasonFilter::checkRecord($serieId, $seasonNumber, 'Season');
		if (isset($validation)) {
			return $validation;
		}

		$model = model('App\Models\CastingModel');
		$model->setPublic(self::isPublic());

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

			$response = Services::response();

			$exists = $model->check($serieId, $seasonNumber, $actorId, $characterId, $rangerId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Casting not found']);
			}
		}
	}
}
