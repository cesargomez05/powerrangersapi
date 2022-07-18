<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

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

			$exists = $model->check($userId, $moduleId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Permission not found');
			}
		}
	}
}
