<?php

namespace App\Entities;

class SeasonVillain extends APIEntity
{
    public function getVillainURI()
    {
        if (isset($this->attributes['villainURI']) && strlen($this->attributes['villainURI'])) {
            return base_url('api/villains/' . $this->attributes['villainURI']);
        }
    }
}
