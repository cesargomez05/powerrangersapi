<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class APIEntity extends Entity
{
	public function getSlugURI()
	{
		if (isset($this->attributes['slugURI']) && strlen($this->attributes['slugURI'])) {
			return base_url('api/' . $this->resource . '/' . $this->attributes['slugURI']);
		}
	}

	public function getPhotoURI()
	{
		if (isset($this->attributes['photoURI']) && strlen($this->attributes['photoURI'])) {
			return base_url('images/' . $this->attributes['photoURI']);
		}
	}
}
