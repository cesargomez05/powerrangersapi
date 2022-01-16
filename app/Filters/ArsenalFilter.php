<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ArsenalFilter implements FilterInterface
{
	public function before(RequestInterface $request, $arguments = null)
	{
		$uri = $request->getUri();
		$arsenalId = $uri->getSegment(2);

		if (!empty($arsenalId)) {
			return self::checkRecord($arsenalId);
		}
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
	}

	public static function checkRecord($arsenalId)
	{
		$response = Services::response();
		$model = model('App\Models\ArsenalModel');

		$validationId = $model->validateId($arsenalId, 'arsenalId', 'Arsenal id');
		if ($validationId !== true) {
			return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['error' => $validationId]);
		}

		$exists = $model->check($arsenalId);
		if (!$exists) {
			return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Arsenal not found']);
		}
	}
}
