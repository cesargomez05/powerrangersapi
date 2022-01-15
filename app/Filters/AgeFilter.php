<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class AgeFilter implements FilterInterface
{
	public function before(RequestInterface $request, $arguments = null)
	{
		$uri = $request->getUri();
		$ageId = $uri->getSegment(2);

		if (!empty($ageId)) {
			return self::checkRecord($ageId);
		}
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
	}

	public static function checkRecord($ageId)
	{
		$response = Services::response();
		$model = model('App\Models\AgeModel');

		$validationId = $model->validateId($ageId, 'ageId', 'Age id');
		if ($validationId !== true) {
			return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
		}

		$exists = $model->check($ageId);
		if (!$exists) {
			return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Actor not found']);
		}
	}
}
