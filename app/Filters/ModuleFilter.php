<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ModuleFilter implements FilterInterface
{
	public function before(RequestInterface $request, $arguments = null)
	{
		$uri = $request->getUri();
		$moduleId = $uri->getSegment(2);

		if (!empty($moduleId)) {
			return self::checkRecord($moduleId);
		}
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
		// Not apply action after filter
	}

	public static function checkRecord($moduleId)
	{
		$response = Services::response();
		$model = model('App\Models\ModuleModel');

		$validationId = $model->validateId($moduleId, 'moduleId', 'Module id');
		if ($validationId !== true) {
			return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['error' => $validationId]);
		}

		$exists = $model->check($moduleId);
		if (!$exists) {
			return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Module not found']);
		}
	}
}
