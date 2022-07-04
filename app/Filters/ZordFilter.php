<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ZordFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($zordId = null)
	{
		$isPublic = self::isPublic();

		$model = model('App\Models\ZordModel');
		$model->setPublic($isPublic);

		if (!empty($zordId)) {
			$response = Services::response();

			$validationId = $model->validateId($zordId, $isPublic ? 'zordSlug' : 'zordId', $isPublic ? 'Zord slug' : 'Zord id');
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($zordId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Zord not found']);
			}
		}
	}
}
