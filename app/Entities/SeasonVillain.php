<?php

namespace App\Entities;

class SeasonVillain extends APIEntity
{
    public function getVillainURI()
    {
        return $this->getURIProperty('villainURI', 'villains');
    }
}
