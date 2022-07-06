<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class APIEntity extends Entity
{
	public function getURI()
	{
		if (isset($this->attributes['URI']) && strlen($this->attributes['URI'])) {
			return base_url('api/' . $this->resource . '/' . $this->attributes['URI']);
		}
	}

	public function getPhotoURI()
	{
		if (isset($this->attributes['photoURI']) && strlen($this->attributes['photoURI'])) {
			return base_url('images/' . $this->attributes['photoURI']);
		}
	}

	public function getRangerURI()
	{
		if (isset($this->attributes['rangerURI']) && strlen($this->attributes['rangerURI'])) {
			return base_url('api/rangers/' . $this->attributes['rangerURI']);
		}
	}

	public function getSeasonURI()
	{
		if (isset($this->attributes['seasonURI']) && strlen($this->attributes['seasonURI'])) {
			return base_url('api/seasons/' . $this->attributes['seasonURI']);
		}
	}

	public function getSerieURI()
	{
		if (isset($this->attributes['serieURI']) && strlen($this->attributes['serieURI'])) {
			return base_url('api/series/' . $this->attributes['serieURI']);
		}
	}

	public function getTransformationURI()
	{
		if (isset($this->attributes['transformationURI']) && strlen($this->attributes['transformationURI'])) {
			return base_url('api/transformations/' . $this->attributes['transformationURI']);
		}
	}

	public function getZordURI()
	{
		if (isset($this->attributes['zordURI']) && strlen($this->attributes['zordURI'])) {
			return base_url('api/zords/' . $this->attributes['zordURI']);
		}
	}

	public function getActorCastingURI()
	{
		if (isset($this->attributes['actorCastingURI']) && strlen($this->attributes['actorCastingURI'])) {
			return base_url('api/castingby/actor/' . $this->attributes['actorCastingURI']);
		}
	}
}
