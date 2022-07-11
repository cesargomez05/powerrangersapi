<?php

namespace App\Entities;

class Ranger extends APIEntity
{
	protected $resource = 'rangers';

	public function getRangerCastingURI()
	{
		return $this->getURIProperty('rangerCastingURI', 'rangercasting');
	}
}
