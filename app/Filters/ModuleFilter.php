<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ModuleFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($moduleId = null)
	{
		$isPublic = self::isPublic();

		$model = model('App\Models\ModuleModel');
		$model->setPublic($isPublic);

		if (!empty($moduleId)) {
			$response = Services::response();

			$validationId = $model->validateId($moduleId, 'moduleId', 'Module id');
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($moduleId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Module not found']);
			}
		}
	}
}
