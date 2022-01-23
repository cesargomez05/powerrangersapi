<?php

namespace App\Entities;

class Casting extends APIEntity
{
	public function getSerieSlugURI()
	{
		if (isset($this->attributes['serieSlugURI']) && strlen($this->attributes['serieSlugURI'])) {
			return base_url('api/series/' . $this->attributes['serieSlugURI']);
		}
	}

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
}
