<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class SerieFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($serieId = null, $module = null)
	{
		$model = model('App\Models\SerieModel');
		$model->setPublic(self::isPublic());

		if (!empty($serieId)) {
			$response = Services::response();

			$validationId = $model->validateId($serieId, $module);
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($serieId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Serie not found']);
			}
		}
	}
}
