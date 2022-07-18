<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ZordFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($zordId = null, $module = null)
	{
		$model = model('App\Models\ZordModel');
		$model->setPublic(self::isPublic());

		if (!empty($zordId)) {
			$validationId = $model->validateId($zordId, $module);
			if ($validationId !== true) {
				return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, $validationId);
			}

			$exists = $model->check($zordId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Zord not found');
			}
		}
	}
}
