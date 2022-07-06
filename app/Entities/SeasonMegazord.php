<?php

namespace App\Entities;

class SeasonMegazord extends APIEntity
{
    public function getMegazordURI()
    {
        return $this->getURIProperty('megazordURI', 'megazords');
    }
}
