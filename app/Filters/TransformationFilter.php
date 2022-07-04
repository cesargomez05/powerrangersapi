<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class TransformationFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($transformationId = null, $module = null)
	{
		$model = model('App\Models\TransformationModel');
		$model->setPublic(self::isPublic());

		if (!empty($transformationId)) {
			$response = Services::response();

			$validationId = $model->validateId($transformationId, $module);
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($transformationId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Transformation not found']);
			}
		}
	}
}
