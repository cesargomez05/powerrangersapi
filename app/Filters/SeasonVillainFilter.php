<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class SeasonVillainFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($serieId, $seasonNumber, $villainId = null)
	{
		$validation = SeasonFilter::checkRecord($serieId, $seasonNumber);
		if (isset($validation)) {
			return $validation;
		}

		$model = model('App\Models\SeasonVillainModel');
		$model->setPublic(self::isPublic());

		if (isset($villainId)) {
			$validation = VillainFilter::checkRecord($villainId);
			if (isset($validation)) {
				return $validation;
			}

			$response = Services::response();
			$exists = $model->check($serieId, $seasonNumber, $villainId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Season-Villain not found']);
			}
		}
	}
}
