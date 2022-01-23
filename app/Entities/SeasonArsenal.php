<?php

namespace App\Entities;

class SeasonArsenal extends APIEntity
{
	public function getArsenalSlugURI()
	{
		if (isset($this->attributes['arsenalSlugURI']) && strlen($this->attributes['arsenalSlugURI'])) {
			return base_url('api/arsenal/' . $this->attributes['arsenalSlugURI']);
		}
	}
}
