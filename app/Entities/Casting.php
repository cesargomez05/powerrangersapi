<?php

namespace App\Entities;

class Casting extends APIEntity
{
	protected $resource = 'casting';

	public function getActorSlugURI()
	{
		if (isset($this->attributes['actorSlugURI']) && strlen($this->attributes['actorSlugURI'])) {
			return base_url('api/actors/' . $this->attributes['actorSlugURI']);
		}
	}

	public function getCharacterSlugURI()
	{
		if (isset($this->attributes['characterSlugURI']) && strlen($this->attributes['characterSlugURI'])) {
			return base_url('api/characters/' . $this->attributes['characterSlugURI']);
		}
	}

	public function getRangerSlugURI()
	{
		if (isset($this->attributes['rangerSlugURI']) && strlen($this->attributes['rangerSlugURI'])) {
			return base_url('api/rangers/' . $this->attributes['rangerSlugURI']);
		}
	}
}
