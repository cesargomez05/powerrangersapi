<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class TransformationRangerFilter implements FilterInterface
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
		// Not apply action after filter
	}

	public static function checkRecord($transformationId, $rangerId = null)
	{
		$validation = TransformationFilter::checkRecord($transformationId);
		if (isset($validation)) {
			return $validation;
		}

		if (isset($rangerId)) {
			$validation = RangerFilter::checkRecord($rangerId);
			if (isset($validation)) {
				return $validation;
			}

			$response = Services::response();
			$model = model('App\Models\TransformationRangerModel');
			$exists = $model->check($transformationId, $rangerId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Transformation-Ranger not found']);
			}
		}
	}
}
