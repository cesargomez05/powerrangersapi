<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SeasonMegazordFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($serieId, $seasonNumber, $megazordId = null)
	{
		$validation = SeasonFilter::checkRecord($serieId, $seasonNumber, 'Season');
		if (isset($validation)) {
			return $validation;
		}

		$model = model('App\Models\SeasonMegazordModel');
		$model->setPublic(self::isPublic());

		if (isset($megazordId)) {
			$validation = MegazordFilter::checkRecord($megazordId, 'Megazord');
			if (isset($validation)) {
				return $validation;
			}

			$exists = $model->check($serieId, $seasonNumber, $megazordId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Season-Megazord not found');
			}
		}
	}
}
