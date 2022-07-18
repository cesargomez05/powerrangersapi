<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MegazordFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($megazordId = null, $module = null)
	{
		$model = model('App\Models\MegazordModel');
		$model->setPublic(self::isPublic());

		if (!empty($megazordId)) {
			$validationId = $model->validateId($megazordId, $module);
			if ($validationId !== true) {
				return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, $validationId);
			}

			$exists = $model->check($megazordId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Megazord not found');
			}
		}
	}
}
