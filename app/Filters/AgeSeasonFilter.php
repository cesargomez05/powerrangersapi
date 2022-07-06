<?php

namespace App\Filters;

use App\Traits\FilterTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class AgeSeasonFilter implements FilterInterface
{
	use FilterTrait;

	public static function checkRecord($slug)
	{
		$validation = AgeFilter::checkRecord($slug, 'Age');
		if (isset($validation)) {
			return $validation;
		}
	}
}
