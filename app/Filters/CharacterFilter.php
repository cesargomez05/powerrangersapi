<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CharacterFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($characterId = null, $module = null)
	{
		$model = model('App\Models\CharacterModel');
		$model->setPublic(self::isPublic());

		if (!empty($characterId)) {
			$validationId = $model->validateId($characterId, $module);
			if ($validationId !== true) {
				return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, $validationId);
			}

			$exists = $model->check($characterId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Character not found');
			}
		}
	}
}
