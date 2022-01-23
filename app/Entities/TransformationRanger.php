<?php

namespace App\Entities;

class TransformationRanger extends APIEntity
{
	public function getTransformationSlugURI()
	{
		if (isset($this->attributes['transformationSlugURI']) && strlen($this->attributes['transformationSlugURI'])) {
			return base_url('api/transformations/' . $this->attributes['transformationSlugURI']);
		}
	}
}
