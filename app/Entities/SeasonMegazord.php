<?php

namespace App\Entities;

class SeasonMegazord extends APIEntity
{
    public function getMegazordURI()
    {
        if (isset($this->attributes['megazordURI']) && strlen($this->attributes['megazordURI'])) {
            return base_url('api/megazords/' . $this->attributes['megazordURI']);
        }
    }
}
