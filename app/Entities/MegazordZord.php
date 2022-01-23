<?php

namespace App\Entities;

class MegazordZord extends APIEntity
{
	public function getMegazordSlugURI()
	{
		if (isset($this->attributes['megazordSlugURI']) && strlen($this->attributes['megazordSlugURI'])) {
			return base_url('api/megazords/' . $this->attributes['megazordSlugURI']);
		}
	}

	public function getZordSlugURI()
	{
		if (isset($this->attributes['zordSlugURI']) && strlen($this->attributes['zordSlugURI'])) {
			return base_url('api/zords/' . $this->attributes['zordSlugURI']);
		}
	}
}
