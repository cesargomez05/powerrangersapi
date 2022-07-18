<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SeasonVillainFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($serieId, $seasonNumber, $villainId = null)
	{
		$validation = SeasonFilter::checkRecord($serieId, $seasonNumber, 'Season');
		if (isset($validation)) {
			return $validation;
		}

		$model = model('App\Models\SeasonVillainModel');
		$model->setPublic(self::isPublic());

		if (isset($villainId)) {
			$validation = VillainFilter::checkRecord($villainId, 'Villain');
			if (isset($validation)) {
				return $validation;
			}

			$exists = $model->check($serieId, $seasonNumber, $villainId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Season-Villain not found');
			}
		}
	}
}
