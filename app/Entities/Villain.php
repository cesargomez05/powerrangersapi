<?php

namespace App\Entities;

class Villain extends APIEntity
{
	protected $resource = 'villains';

	public function getVillainSeasonURI()
	{
		return $this->getURIProperty('villainSeasonURI', 'villainseason');
	}
}
