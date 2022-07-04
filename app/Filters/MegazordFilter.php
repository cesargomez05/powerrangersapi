<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class MegazordFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($megazordId = null)
	{
		$isPublic = self::isPublic();

		$model = model('App\Models\MegazordModel');
		$model->setPublic($isPublic);

		if (!empty($megazordId)) {
			$response = Services::response();

			$validationId = $model->validateId($megazordId, 'megazordId', 'Megazord id');
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($megazordId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Megazord not found']);
			}
		}
	}
}
