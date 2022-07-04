<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

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
			$response = Services::response();
			$exists = $model->check($rangerId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Ranger morpher not found']);
			}
		}
	}
}
