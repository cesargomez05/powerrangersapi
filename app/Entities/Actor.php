<?php

namespace App\Entities;

class Actor extends APIEntity
{
	protected $resource = 'actors';

	public function getActorCastingURI()
	{
		return $this->getURIProperty('actorCastingURI', 'actorcasting');
	}
}
