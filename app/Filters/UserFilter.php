<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class UserFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($userId = null, $module = null)
	{
		$model = model('App\Models\UserModel');
		$model->setPublic(self::isPublic());

		if (!empty($userId)) {
			$validationId = $model->validateId($userId, $module);
			if ($validationId !== true) {
				return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, $validationId);
			}

			$exists = $model->check($userId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'User not found');
			}
		}
	}
}
