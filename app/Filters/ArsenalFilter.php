<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ArsenalFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($arsenalId = null)
	{
		$isPublic = self::isPublic();

		$model = model('App\Models\ArsenalModel');
		$model->setPublic($isPublic);

		if (!empty($arsenalId)) {
			$response = Services::response();

			$validationId = $model->validateId($arsenalId, $isPublic ? 'arsenalSlug' : 'arsenalId', $isPublic ? 'Arsenal slug' : 'Arsenal id');
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
