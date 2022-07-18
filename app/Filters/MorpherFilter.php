<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MorpherFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($morpherId = null, $module = null)
	{
		$model = model('App\Models\MorpherModel');
		$model->setPublic(self::isPublic());

		if (!empty($morpherId)) {
			$validationId = $model->validateId($morpherId, $module);
			if ($validationId !== true) {
				return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, $validationId);
			}

			$exists = $model->check($morpherId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Morpher not found');
			}
		}
	}
}
