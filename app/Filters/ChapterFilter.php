<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ChapterFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($serieId, $seasonNumber, $number = null)
	{
		$seasonValidation = SeasonFilter::checkRecord($serieId, $seasonNumber);
		if (isset($seasonValidation)) {
			return $seasonValidation;
		}

		$model = model('App\Models\ChapterModel');
		$model->setPublic(self::isPublic());

		if (isset($number)) {
			$response = Services::response();

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
