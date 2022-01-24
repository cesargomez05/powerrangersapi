<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class RangerMorpherFilter implements FilterInterface
{
	public function before(RequestInterface $request, $arguments = null)
	{
		$uri = $request->getUri();
		$rangerId = $uri->getSegment(2);
		return self::checkRecord($rangerId, $request->getMethod());
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
		// Not apply action after filter
	}

	public static function checkRecord($rangerId, $requestType)
	{
		$rangerValidation = RangerFilter::checkRecord($rangerId);
		if (isset($rangerValidation)) {
			return $rangerValidation;
		}

		if (in_array($requestType, ['get', 'delete'])) {
			$response = Services::response();
			$model = model('App\Models\RangerMorpherModel');

			$exists = $model->check($rangerId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Ranger morpher not found']);
			}
		}
	}
}
