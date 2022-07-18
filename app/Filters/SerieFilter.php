<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SerieFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($serieId = null, $module = null)
	{
		$model = model('App\Models\SerieModel');
		$model->setPublic(self::isPublic());

		if (!empty($serieId)) {
			$validationId = $model->validateId($serieId, $module);
			if ($validationId !== true) {
				return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, $validationId);
			}

			$exists = $model->check($serieId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Serie not found');
			}
		}
	}
}
