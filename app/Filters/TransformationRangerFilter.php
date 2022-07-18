<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class TransformationRangerFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($transformationId, $rangerId = null)
	{
		$validation = TransformationFilter::checkRecord($transformationId, 'Transformation');
		if (isset($validation)) {
			return $validation;
		}

		$model = model('App\Models\TransformationRangerModel');
		$model->setPublic(self::isPublic());

		if (isset($rangerId)) {
			$validation = RangerFilter::checkRecord($rangerId, 'Ranger');
			if (isset($validation)) {
				return $validation;
			}

			$exists = $model->check($transformationId, $rangerId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Transformation-Ranger not found');
			}
		}
	}
}
