<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ArsenalFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($arsenalId = null, $module = null)
	{
		$model = model('App\Models\ArsenalModel');
		$model->setPublic(self::isPublic());

		if (!empty($arsenalId)) {
			$validationId = $model->validateId($arsenalId, $module);
			if ($validationId !== true) {
				return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, $validationId);
			}

			$exists = $model->check($arsenalId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Arsenal not found');
			}
		}
	}
}
