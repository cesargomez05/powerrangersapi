<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class VillainFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($villainId = null, $module = null)
	{
		$model = model('App\Models\VillainModel');
		$model->setPublic(self::isPublic());

		if (!empty($villainId)) {
			$response = Services::response();

			$validationId = $model->validateId($villainId, $module);
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($villainId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Villain not found']);
			}
		}
	}
}
