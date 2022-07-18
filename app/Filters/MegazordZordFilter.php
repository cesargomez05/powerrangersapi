<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MegazordZordFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($megazordId, $zordId = null)
	{
		$validation = MegazordFilter::checkRecord($megazordId, 'Megazord');
		if (isset($validation)) {
			return $validation;
		}

		$model = model('App\Models\MegazordZordModel');
		$model->setPublic(self::isPublic());

		if (isset($zordId)) {
			$validation = ZordFilter::checkRecord($zordId, 'Zord');
			if (isset($validation)) {
				return $validation;
			}

			$exists = $model->check($megazordId, $zordId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Megazord-Zord not found');
			}
		}
	}
}
