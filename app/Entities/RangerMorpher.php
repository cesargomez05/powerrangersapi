<?php

namespace App\Entities;

class RangerMorpher extends APIEntity
{
    public function getMorpherURI()
    {
        if (isset($this->attributes['morpherURI']) && strlen($this->attributes['morpherURI'])) {
            return base_url('api/morphers/' . $this->attributes['morpherURI']);
        }
    }
}
