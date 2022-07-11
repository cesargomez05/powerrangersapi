<?php

namespace App\Entities;

class Arsenal extends APIEntity
{
	protected $resource = 'arsenal';

	public function getArsenalSeasonURI()
	{
		return $this->getURIProperty('arsenalSeasonURI', 'arsenalseason');
	}
}
