<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class CharacterFilter implements FilterInterface
{
	public function before(RequestInterface $request, $arguments = null)
	{
		$uri = $request->getUri();
		$characterId = $uri->getSegment(2);

		if (!empty($characterId)) {
			return self::checkRecord($characterId);
		}
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
	}

	public static function checkRecord($characterId)
	{
		$response = Services::response();
		$model = model('App\Models\CharacterModel');

		$validationId = $model->validateId($characterId, 'characterId', 'Character id');
		if ($validationId !== true) {
			return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['errors' => $validationId]);
		}

		$exists = $model->check($characterId);
		if (!$exists) {
			return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'Character not found']);
		}
	}
}
