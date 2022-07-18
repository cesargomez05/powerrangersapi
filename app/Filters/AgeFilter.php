<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AgeFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($ageId = null, $module = null)
	{
		$model = model('App\Models\AgeModel');
		$model->setPublic(self::isPublic());

		if (!empty($ageId)) {
			$validationId = $model->validateId($ageId, $module);
			if ($validationId !== true) {
				return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, $validationId);
			}

			$exists = $model->check($ageId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Age not found');
			}
		}
	}
}
