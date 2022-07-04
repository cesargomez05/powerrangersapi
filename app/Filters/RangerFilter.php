<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class RangerFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($rangerId = null)
	{
		$isPublic = self::isPublic();

		$model = model('App\Models\RangerModel');
		$model->setPublic($isPublic);

		if (!empty($rangerId)) {
			$response = Services::response();

			$validationId = $model->validateId($rangerId, $isPublic ? 'rangerSlug' : 'rangerId', $isPublic ? 'Ranger slug' : 'Ranger id');
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($rangerId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Ranger not found']);
			}
		}
	}
}
