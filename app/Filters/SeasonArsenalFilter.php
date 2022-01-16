<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class SeasonArsenalFilter implements FilterInterface
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

	public static function checkRecord($serieId, $seasonNumber, $arsenalId = null)
	{
		$validation = SeasonFilter::checkRecord($serieId, $seasonNumber);
		if (isset($validation)) {
			return $validation;
		}

		if (isset($arsenalId)) {
			$validation = ArsenalFilter::checkRecord($arsenalId);
			if (isset($validation)) {
				return $validation;
			}

			$response = Services::response();
			$model = model('App\Models\SeasonArsenalModel');
			$exists = $model->check($serieId, $seasonNumber, $arsenalId);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Season-Arsenal not found']);
			}
		}
	}
}
