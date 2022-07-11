<?php

namespace App\Entities;

class Age extends APIEntity
{
	protected $resource = 'ages';

	public function getAgeSeasonURI()
	{
		return $this->getURIProperty('ageSeasonURI', 'ageseason');
	}
}
