<?php

namespace App\Entities;

class Megazord extends APIEntity
{
	protected $resource = 'megazords';

	public function getMegazordzordURI()
	{
		return $this->getURIProperty('megazordZordURI', 'megazordzord');
	}
}
