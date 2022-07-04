<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class SeasonFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($serieId, $number = null, $module = null)
	{
		$serieValidation = SerieFilter::checkRecord($serieId, 'Serie');
		if (isset($serieValidation)) {
			return $serieValidation;
		}

		$model = model('App\Models\SeasonModel');
		$model->setPublic(self::isPublic());

		if (isset($number)) {
			$response = Services::response();

			$validationId = $model->validateId($number, $module, 'Number', 'Number');
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($serieId, $number);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Season not found']);
			}
		}
	}
}
