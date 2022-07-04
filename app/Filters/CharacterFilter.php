<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class CharacterFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($characterId = null, $module = null)
	{
		$model = model('App\Models\CharacterModel');
		$model->setPublic(self::isPublic());

		if (!empty($characterId)) {
			$response = Services::response();

			$validationId = $model->validateId($characterId, $module);
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($characterId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Character not found']);
			}
		}
	}
}
