<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class SeasonFilter implements FilterInterface
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

	public static function checkRecord($serieId, $number = null)
	{
		$serieValidation = SerieFilter::checkRecord($serieId);
		if (isset($serieValidation)) {
			return $serieValidation;
		}

		if (isset($number)) {
			$response = Services::response();
			$model = model('App\Models\SeasonModel');

			$validationId = $model->validateId($number, 'number', 'Season number');
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($serieId, $number);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Season not found']);
			}
		}
	}
}
