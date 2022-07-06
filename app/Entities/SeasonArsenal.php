<?php

namespace App\Entities;

class SeasonArsenal extends APIEntity
{
    public function getArsenalURI()
    {
        return $this->getURIProperty('arsenalURI', 'arsenal');
    }
}
