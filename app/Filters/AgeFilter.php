<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class AgeFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($ageId = null, $module = null)
	{
		$model = model('App\Models\AgeModel');
		$model->setPublic(self::isPublic());

		if (!empty($ageId)) {
			$response = Services::response();

			$validationId = $model->validateId($ageId, $module);
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($ageId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Age not found']);
			}
		}
	}
}
