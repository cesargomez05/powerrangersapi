<?php

namespace App\Entities;

class Megazord extends APIEntity
{
	protected $resource = 'megazords';

	public function getMegazordzordURI()
	{
		if (isset($this->attributes['megazordZordURI']) && strlen($this->attributes['megazordZordURI'])) {
			return base_url('api/megazordzord/' . $this->attributes['megazordZordURI']);
		}
	}
}
