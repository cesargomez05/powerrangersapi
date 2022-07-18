<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class TransformationFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($transformationId = null, $module = null)
	{
		$model = model('App\Models\TransformationModel');
		$model->setPublic(self::isPublic());

		if (!empty($transformationId)) {
			$validationId = $model->validateId($transformationId, $module);
			if ($validationId !== true) {
				return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, $validationId);
			}

			$exists = $model->check($transformationId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Transformation not found');
			}
		}
	}
}
