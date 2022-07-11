<?php

namespace App\Entities;

class Character extends APIEntity
{
	protected $resource = 'characters';

	public function getCharacterCastingURI()
	{
		return $this->getURIProperty('characterCastingURI', 'charactercasting');
	}
}
