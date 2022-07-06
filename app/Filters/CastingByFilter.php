<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class CastingByFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($type, $slug)
	{
		switch ($type) {
			case 'actor':
				$validation = ActorFilter::checkRecord($slug, 'Actor');
				break;
			case 'character':
				$validation = CharacterFilter::checkRecord($slug, 'Character');
				break;
			case 'ranger':
				$validation = RangerFilter::checkRecord($slug, 'Ranger');
				break;
			default:
				$response = Services::response();
				return $response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['error' => $type, 'slug' => $slug]);
		}

		if (isset($validation)) {
			return $validation;
		}
	}
}
