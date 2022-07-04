<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ArsenalFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($arsenalId = null, $module = null)
	{
		$model = model('App\Models\ArsenalModel');
		$model->setPublic(self::isPublic());

		if (!empty($arsenalId)) {
			$response = Services::response();

			$validationId = $model->validateId($arsenalId, $module);
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($arsenalId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Arsenal not found']);
			}
		}
	}
}
