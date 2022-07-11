<?php

namespace App\Entities;

class Transformation extends APIEntity
{
	protected $resource = 'transformations';

	public function getTransformationRangerURI()
	{
		return $this->getURIProperty('transformationRangerURI', 'transformationranger');
	}
}
