<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RangerFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($rangerId = null, $module = null)
	{
		$model = model('App\Models\RangerModel');
		$model->setPublic(self::isPublic());

		if (!empty($rangerId)) {
			$validationId = $model->validateId($rangerId, $module);
			if ($validationId !== true) {
				return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, $validationId);
			}

			$exists = $model->check($rangerId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Ranger not found');
			}
		}
	}
}
