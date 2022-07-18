<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ModuleFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($moduleId = null, $module = null)
	{
		$model = model('App\Models\ModuleModel');
		$model->setPublic(self::isPublic());

		if (!empty($moduleId)) {
			$validationId = $model->validateId($moduleId, $module);
			if ($validationId !== true) {
				return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, $validationId);
			}

			$exists = $model->check($moduleId);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Module not found');
			}
		}
	}
}
