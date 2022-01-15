<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class SeasonFilter implements FilterInterface
{
	/**
	 * Do whatever processing this filter needs to do.
	 * By default it should not return anything during
	 * normal execution. However, when an abnormal state
	 * is found, it should return an instance of
	 * CodeIgniter\HTTP\Response. If it does, script
	 * execution will end and that Response will be
	 * sent back to the client, allowing for error pages,
	 * redirects, etc.
	 *
	 * @param RequestInterface $request
	 * @param array|null $arguments
	 *
	 * @return mixed
	 */
	public function before(RequestInterface $request, $arguments = null)
	{
		header('Content-type: application/json');

		$uri = $request->getUri();
		$serieId = $uri->getSegment(2);
		$seasonNumber = $uri->getSegment(3);

		$serieValidation = SerieFilter::checkSerie($serieId);
		if (isset($serieValidation)) {
			return $serieValidation;
		}
		return self::checkSeason($serieId, $seasonNumber);
	}

	/**
	 * Allows After filters to inspect and modify the response
	 * object as needed. This method does not allow any way
	 * to stop execution of other after filters, short of
	 * throwing an Exception or Error.
	 *
	 * @param RequestInterface $request
	 * @param ResponseInterface $response
	 * @param array|null $arguments
	 *
	 * @return mixed
	 */
	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
		//
	}

	public static function checkSeason($serieId, $seasonNumber)
	{
		$seasonModel = model('App\Models\SeasonModel');

		$validationId = $seasonModel->validateId($serieId, $seasonNumber);
		if ($validationId !== true) {
			return Services::response()->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['error' => $validationId]);
		}

		$season = $seasonModel->get($serieId, $seasonNumber);
		if (!isset($season)) {
			return Services::response()->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Season not exists']);
		}
	}
}
