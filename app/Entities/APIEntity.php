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

	public function getSeasonSlugURI()
	{
		if (isset($this->attributes['seasonSlugURI']) && strlen($this->attributes['seasonSlugURI'])) {
			return base_url('api/seasons/' . $this->attributes['seasonSlugURI']);
		}
	}

	public function getChapterSlugURI()
	{
		if (isset($this->attributes['chapterSlugURI']) && strlen($this->attributes['chapterSlugURI'])) {
			return base_url('api/chapters/' . $this->attributes['chapterSlugURI']);
		}
	}

	public function getCastingSlugURI()
	{
		if (isset($this->attributes['castingSlugURI']) && strlen($this->attributes['castingSlugURI'])) {
			return base_url('api/casting/' . $this->attributes['castingSlugURI']);
		}
	}

	public function getRangerSlugURI()
	{
		if (isset($this->attributes['rangerSlugURI']) && strlen($this->attributes['rangerSlugURI'])) {
			return base_url('api/rangers/' . $this->attributes['rangerSlugURI']);
		}
	}
}
