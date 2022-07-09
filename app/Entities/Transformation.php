<?php

namespace App\Entities;

class Transformation extends APIEntity
{
	protected $resource = 'transformations';

	public function getTransformationRangersURI()
	{
		return $this->getURIProperty('transformationRangersURI', 'transformationrangers');
	}
}
