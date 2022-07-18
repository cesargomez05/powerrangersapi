<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SeasonArsenalFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($serieId, $seasonNumber, $arsenalId = null)
	{
		$validation = SeasonFilter::checkRecord($serieId, $seasonNumber, 'Season');
		if (isset($validation)) {
			return $validation;
		}

		$model = model('App\Models\SeasonArsenalModel');
		$model->setPublic(self::isPublic());

		if (isset($arsenalId)) {
			$validation = ArsenalFilter::checkRecord($arsenalId, 'Arsenal');
			if (isset($validation)) {
				return $validation;
			}

			$exists = $model->check($serieId, $seasonNumber, $arsenalId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Season-Arsenal not found');
			}
		}
	}
}
