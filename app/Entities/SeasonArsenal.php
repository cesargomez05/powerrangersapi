<?php

namespace App\Entities;

class SeasonArsenal extends APIEntity
{
    public function getArsenalURI()
    {
        if (isset($this->attributes['arsenalURI']) && strlen($this->attributes['arsenalURI'])) {
            return base_url('api/arsenal/' . $this->attributes['arsenalURI']);
        }
    }
}
