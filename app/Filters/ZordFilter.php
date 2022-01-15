<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ZordFilter implements FilterInterface
{
	public function before(RequestInterface $request, $arguments = null)
	{
		$uri = $request->getUri();
		$zordId = $uri->getSegment(2);

		if (!empty($zordId)) {
			return self::checkRecord($zordId);
		}
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
	}

	public static function checkRecord($zordId)
	{
		$response = Services::response();
		$model = model('App\Models\ZordModel');

		$validationId = $model->validateId($zordId, 'zordId', 'Zord id');
		if ($validationId !== true) {
			return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
		}

		$exists = $model->check($zordId);
		if (!$exists) {
			return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Zord not found']);
		}
	}
}
