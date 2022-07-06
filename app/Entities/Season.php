<?php

namespace App\Entities;

class Season extends APIEntity
{
	protected $resource = 'seasons';

	public function getCastingURI()
	{
		return $this->getURIProperty('castingURI', 'casting');
	}

	public function getChapterURI()
	{
		return $this->getURIProperty('chapterURI', 'chapters');
	}

	public function getSeasonArsenalURI()
	{
		return $this->getURIProperty('seasonArsenalURI', 'seasonarsenal');
	}

	public function getSeasonMegazordURI()
	{
		return $this->getURIProperty('seasonMegazordURI', 'seasonmegazord');
	}

	public function getSeasonVillainURI()
	{
		return $this->getURIProperty('seasonVillainURI', 'seasonvillain');
	}

	public function getSeasonZordURI()
	{
		return $this->getURIProperty('seasonZordURI', 'seasonzord');
	}

	public function getTeamupURI()
	{
		return $this->getURIProperty('teamupURI', 'teamup');
	}
}
