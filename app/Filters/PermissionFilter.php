<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class PermissionFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($userId, $moduleId = null, $module = null)
	{
		$validation = UserFilter::checkRecord($userId, 'User');
		if (isset($validation)) {
			return $validation;
		}

		$model = model('App\Models\PermissionModel');
		$model->setPublic(self::isPublic());

		if (isset($moduleId)) {
			$validation = ModuleFilter::checkRecord($moduleId, 'Module');
			if (isset($validation)) {
				return $validation;
			}

			$response = Services::response();
			$exists = $model->check($userId, $moduleId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Permission not found']);
			}
		}
	}
}
