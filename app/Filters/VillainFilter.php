<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class VillainFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($villainId = null, $module = null)
	{
		$model = model('App\Models\VillainModel');
		$model->setPublic(self::isPublic());

		if (!empty($villainId)) {
			$validationId = $model->validateId($villainId, $module);
			if ($validationId !== true) {
				return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, $validationId);
			}

			$exists = $model->check($villainId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Villain not found');
			}
		}
	}
}
