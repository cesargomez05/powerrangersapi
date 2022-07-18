<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SeasonZordFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($serieId, $seasonNumber, $zordId = null)
	{
		$validation = SeasonFilter::checkRecord($serieId, $seasonNumber, 'Season');
		if (isset($validation)) {
			return $validation;
		}

		$model = model('App\Models\SeasonZordModel');
		$model->setPublic(self::isPublic());

		if (isset($zordId)) {
			$validation = ZordFilter::checkRecord($zordId, 'Zord');
			if (isset($validation)) {
				return $validation;
			}

			$exists = $model->check($serieId, $seasonNumber, $zordId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Season-Zord not found');
			}
		}
	}
}
