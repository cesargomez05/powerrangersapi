<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SerieFilter implements FilterInterface
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
		$serieId = $request->getUri()->getSegment(2);

		return self::checkSerie($serieId);
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

	public static function checkSerie($serieId)
	{
		$serieModel = model('App\Models\SerieModel');

		$validationId = $serieModel->validateId($serieId, 'serieId', 'Serie id is not valid');
		if ($validationId !== true) {
			header('HTTP/1.1 ' . 500);
			die(json_encode(['errors' => $validationId]));
		}

		$serie = $serieModel->get($serieId);
		if (!isset($serie)) {
			header('HTTP/1.1 ' . 404);
			die(json_encode([
				'status' => 404,
				'error' => 404,
				'messages' => [
					"error" => "Record not found"
				]
			]));
		}
	}
}
