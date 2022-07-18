<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RangerMorpherFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($rangerId, $requestType = null)
	{
		$rangerValidation = RangerFilter::checkRecord($rangerId, 'Ranger');
		if (isset($rangerValidation)) {
			return $rangerValidation;
		}

		$model = model('App\Models\RangerMorpherModel');
		$model->setPublic(self::isPublic());

		if (in_array($requestType, ['get', 'delete'])) {
			$exists = $model->check($rangerId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Ranger morpher not found');
			}
		}
	}
}
