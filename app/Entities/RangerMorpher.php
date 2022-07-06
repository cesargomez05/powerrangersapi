<?php

namespace App\Entities;

class RangerMorpher extends APIEntity
{
    public function getMorpherURI()
    {
        return $this->getURIProperty('morpherURI', 'morphers');
    }
}
