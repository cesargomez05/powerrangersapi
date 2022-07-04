<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class SeasonZordFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($serieId, $seasonNumber, $zordId = null)
	{
		$validation = SeasonFilter::checkRecord($serieId, $seasonNumber);
		if (isset($validation)) {
			return $validation;
		}

		$model = model('App\Models\SeasonZordModel');
		$model->setPublic(self::isPublic());

		if (isset($zordId)) {
			$validation = ZordFilter::checkRecord($zordId);
			if (isset($validation)) {
				return $validation;
			}

			$response = Services::response();
			$exists = $model->check($serieId, $seasonNumber, $zordId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Season-Zord not found']);
			}
		}
	}
}
