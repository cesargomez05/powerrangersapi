<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class MegazordFilter implements FilterInterface
{
	public function before(RequestInterface $request, $arguments = null)
	{
		$uri = $request->getUri();
		$megazordId = $uri->getSegment(2);

		if (!empty($megazordId)) {
			return self::checkRecord($megazordId);
		}
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
	}

	public static function checkRecord($megazordId)
	{
		$response = Services::response();
		$model = model('App\Models\MegazordModel');

		$validationId = $model->validateId($megazordId, 'megazordId', 'Megazord id');
		if ($validationId !== true) {
			return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
		}

		$exists = $model->check($megazordId);
		if (!$exists) {
			return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Megazord not found']);
		}
	}
}
