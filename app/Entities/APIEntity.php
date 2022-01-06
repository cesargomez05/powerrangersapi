<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class APIEntity extends Entity
{
	public function getIdURI()
	{
		if (isset($this->resource) && strlen($this->resource)) {
			if (isset($this->attributes['idURI']) && strlen($this->attributes['idURI'])) {
				return base_url($this->resource . '/' . $this->attributes['idURI']);
			}
		}
	}

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
