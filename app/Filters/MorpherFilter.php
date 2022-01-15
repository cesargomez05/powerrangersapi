<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class MorpherFilter implements FilterInterface
{
	public function before(RequestInterface $request, $arguments = null)
	{
		$uri = $request->getUri();
		$morpherId = $uri->getSegment(2);

		if (!empty($morpherId)) {
			return self::checkRecord($morpherId);
		}
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
	}

	public static function checkRecord($morpherId)
	{
		$response = Services::response();
		$model = model('App\Models\MorpherModel');

		$validationId = $model->validateId($morpherId, 'morpherId', 'Morpher id');
		if ($validationId !== true) {
			return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['error' => $validationId]);
		}

		$exists = $model->check($morpherId);
		if (!$exists) {
			$response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Morpher not found']);
		}
	}
}
