<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class PermissionFilter implements FilterInterface
{
	public function before(RequestInterface $request, $arguments = null)
	{
		$uri = $request->getUri();
		$segments = $uri->getSegments();
		array_shift($segments);
		return call_user_func_array([$this, 'checkRecord'], $segments);
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
	}

	public static function checkRecord($userId, $moduleId = null)
	{
		$validation = UserFilter::checkRecord($userId);
		if (isset($validation)) {
			return $validation;
		}

		if (isset($moduleId)) {
			$validation = ModuleFilter::checkRecord($moduleId);
			if (isset($validation)) {
				return $validation;
			}

			$response = Services::response();
			$model = model('App\Models\PermissionModel');
			$exists = $model->check($userId, $moduleId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Permission not found']);
			}
		}
	}
}
