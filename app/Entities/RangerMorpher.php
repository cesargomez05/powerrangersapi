<?php

namespace App\Entities;

class RangerMorpher extends APIEntity
{
	public function getMorpherSlugURI()
	{
		if (isset($this->attributes['morpherSlugURI']) && strlen($this->attributes['morpherSlugURI'])) {
			return base_url('api/morphers/' . $this->attributes['morpherSlugURI']);
		}
	}
}
