<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class MorpherFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($morpherId = null)
	{
		$isPublic = self::isPublic();

		$model = model('App\Models\MorpherModel');
		$model->setPublic($isPublic);

		if (!empty($morpherId)) {
			$response = Services::response();

			$validationId = $model->validateId($morpherId, 'morpherId', 'Morpher id');
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($morpherId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Morpher not found']);
			}
		}
	}
}
