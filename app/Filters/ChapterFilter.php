<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ChapterFilter implements FilterInterface
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

	public static function checkRecord($serieId, $seasonNumber, $number = null)
	{
		$seasonValidation = SeasonFilter::checkRecord($serieId, $seasonNumber);
		if (isset($seasonValidation)) {
			return $seasonValidation;
		}

		if (isset($number)) {
			$response = Services::response();
			$model = model('App\Models\ChapterModel');

			$validationId = $model->validateId($number, 'number', 'Chapter number');
			if ($validationId !== true) {
				return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
			}

			$exists = $model->check($serieId, $seasonNumber, $number);
			if (!$exists) {
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Chapter not found']);
			}
		}
	}
}
