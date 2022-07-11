<?php

namespace App\Entities;

class Morpher extends APIEntity
{
	protected $resource = 'morphers';

	public function getMorpherRangerURI()
	{
		return $this->getURIProperty('morpherRangerURI', 'morpherranger');
	}
}
