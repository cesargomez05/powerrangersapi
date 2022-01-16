<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class UserFilter implements FilterInterface
{
	public function before(RequestInterface $request, $arguments = null)
	{
		$uri = $request->getUri();
		$userId = $uri->getSegment(2);

		if (!empty($userId)) {
			return self::checkRecord($userId);
		}
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
	}

	public static function checkRecord($userId)
	{
		$response = Services::response();
		$model = model('App\Models\UserModel');

		$validationId = $model->validateId($userId, 'userId', 'User id');
		if ($validationId !== true) {
			return $response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['error' => $validationId]);
		}

		$exists = $model->check($userId);
		if (!$exists) {
			return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => 'User not found']);
		}
	}
}
