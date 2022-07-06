<?php

namespace App\Entities;

class Season extends APIEntity
{
	protected $resource = 'seasons';

	public function getCastingURI()
	{
		if (isset($this->attributes['castingURI']) && strlen($this->attributes['castingURI'])) {
			return base_url('api/casting/' . $this->attributes['castingURI']);
		}
	}

	public function getChapterURI()
	{
		if (isset($this->attributes['chapterURI']) && strlen($this->attributes['chapterURI'])) {
			return base_url('api/chapters/' . $this->attributes['chapterURI']);
		}
	}

	public function getSeasonArsenalURI()
	{
		if (isset($this->attributes['seasonArsenalURI']) && strlen($this->attributes['seasonArsenalURI'])) {
			return base_url('api/seasonarsenal/' . $this->attributes['seasonArsenalURI']);
		}
	}

	public function getSeasonMegazordURI()
	{
		if (isset($this->attributes['seasonMegazordURI']) && strlen($this->attributes['seasonMegazordURI'])) {
			return base_url('api/seasonmegazord/' . $this->attributes['seasonMegazordURI']);
		}
	}

	public function getSeasonVillainURI()
	{
		if (isset($this->attributes['seasonVillainURI']) && strlen($this->attributes['seasonVillainURI'])) {
			return base_url('api/seasonvillain/' . $this->attributes['seasonVillainURI']);
		}
	}

	public function getSeasonZordURI()
	{
		if (isset($this->attributes['seasonZordURI']) && strlen($this->attributes['seasonZordURI'])) {
			return base_url('api/seasonzord/' . $this->attributes['seasonZordURI']);
		}
	}

	public function getTeamupURI()
	{
		if (isset($this->attributes['teamupURI']) && strlen($this->attributes['teamupURI'])) {
			return base_url('api/teamup/' . $this->attributes['teamupURI']);
		}
	}
}
