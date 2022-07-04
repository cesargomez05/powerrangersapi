<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ActorFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($actorId = null, $module = null)
	{
		$model = model('App\Models\ActorModel');
		$model->setPublic(self::isPublic());

		if (!empty($actorId)) {
			$response = Services::response();

			$validationId = $model->validateId($actorId, $module);
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($actorId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Actor not found']);
			}
		}
	}
}
