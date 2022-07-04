<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class SeasonMegazordFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($serieId, $seasonNumber, $megazordId = null)
	{
		$validation = SeasonFilter::checkRecord($serieId, $seasonNumber);
		if (isset($validation)) {
			return $validation;
		}

		$model = model('App\Models\SeasonMegazordModel');
		$model->setPublic(self::isPublic());

		if (isset($megazordId)) {
			$validation = MegazordFilter::checkRecord($megazordId);
			if (isset($validation)) {
				return $validation;
			}

			$response = Services::response();
			$exists = $model->check($serieId, $seasonNumber, $megazordId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Season-Megazord not found']);
			}
		}
	}
}
