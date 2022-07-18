<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ChapterFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($serieId, $seasonNumber, $number = null)
	{
		$seasonValidation = SeasonFilter::checkRecord($serieId, $seasonNumber, 'Season');
		if (isset($seasonValidation)) {
			return $seasonValidation;
		}

		$model = model('App\Models\ChapterModel');
		$model->setPublic(self::isPublic());

		if (isset($number)) {
			$validationId = $model->validateId($number, null, 'Number', 'Number');
			if ($validationId !== true) {
				return self::throwError(ResponseInterface::HTTP_BAD_REQUEST, $validationId);
			}

			$exists = $model->check($serieId, $seasonNumber, $number);
			if (!$exists) {
				return self::throwError(ResponseInterface::HTTP_NOT_FOUND, 'Chapter not found');
			}
		}
	}
}
