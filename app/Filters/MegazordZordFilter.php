<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class MegazordZordFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($megazordId, $zordId = null)
	{
		$validation = MegazordFilter::checkRecord($megazordId, 'Megazord');
		if (isset($validation)) {
			return $validation;
		}

		$model = model('App\Models\MegazordZordModel');
		$model->setPublic(self::isPublic());

		if (isset($zordId)) {
			$validation = ZordFilter::checkRecord($zordId, 'Zord');
			if (isset($validation)) {
				return $validation;
			}

			$response = Services::response();
			$exists = $model->check($megazordId, $zordId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Megazord-Zord not found']);
			}
		}
	}
}
