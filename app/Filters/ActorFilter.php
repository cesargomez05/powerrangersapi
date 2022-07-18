<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ActorFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($actorId = null, $module = null)
	{
		$model = model('App\Models\ActorModel');
		$model->setPublic(self::isPublic());

		if (!empty($actorId)) {
			$validationId = $model->validateId($actorId, $module);
			if ($validationId !== true) {
				return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, $validationId);
			}

			$exists = $model->check($actorId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Actor not found');
			}
		}
	}
}
