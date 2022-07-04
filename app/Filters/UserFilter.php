<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class UserFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($userId = null, $module = null)
	{
		$model = model('App\Models\UserModel');
		$model->setPublic(self::isPublic());

		if (!empty($userId)) {
			$response = Services::response();

			$validationId = $model->validateId($userId, $module);
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($userId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'User not found']);
			}
		}
	}
}
