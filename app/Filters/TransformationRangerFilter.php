<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

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

			$response = Services::response();
			$exists = $model->check($transformationId, $rangerId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Transformation-Ranger not found']);
			}
		}
	}
}
