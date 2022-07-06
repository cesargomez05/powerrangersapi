<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class APIEntity extends Entity
{
	protected function getURIProperty($propertyKey, $moduleKey)
	{
		if (isset($this->attributes[$propertyKey]) && strlen($this->attributes[$propertyKey])) {
			return base_url('api/' . $moduleKey . '/' . $this->attributes[$propertyKey]);
		}
	}

	public function getURI()
	{
		return $this->getURIProperty('URI', $this->resource);
	}

	public function getPhotoURI()
	{
		if (isset($this->attributes['photoURI']) && strlen($this->attributes['photoURI'])) {
			return base_url('images/' . $this->attributes['photoURI']);
		}
	}

	public function getRangerURI()
	{
		return $this->getURIProperty('rangerURI', 'rangers');
	}

	public function getSeasonURI()
	{
		return $this->getURIProperty('seasonURI', 'seasons');
	}

	public function getSerieURI()
	{
		return $this->getURIProperty('serieURI', 'series');
	}

	public function getTransformationURI()
	{
		return $this->getURIProperty('transformationURI', 'transformations');
	}

	public function getZordURI()
	{
		return $this->getURIProperty('zordURI', 'zords');
	}
}
