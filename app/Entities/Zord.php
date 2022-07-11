<?php

namespace App\Entities;

class Zord extends APIEntity
{
	protected $resource = 'zords';

	public function getZordMegazordURI()
	{
		return $this->getURIProperty('zordMegazordURI', 'zordmegazord');
	}

	public function getZordSeasonURI()
	{
		return $this->getURIProperty('zordSeasonURI', 'zordseason');
	}
}
