<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class MegazordZordFilter implements FilterInterface
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

	public static function checkRecord($megazordId, $zordId = null)
	{
		$validation = MegazordFilter::checkRecord($megazordId);
		if (isset($validation)) {
			return $validation;
		}

		if (isset($zordId)) {
			$validation = ZordFilter::checkRecord($zordId);
			if (isset($validation)) {
				return $validation;
			}

			$response = Services::response();
			$model = model('App\Models\MegazordZordModel');
			$exists = $model->check($megazordId, $zordId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Megazord-Zord not found']);
			}
		}
	}
}
