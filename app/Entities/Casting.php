<?php

namespace App\Entities;

class Casting extends APIEntity
{
    public function getActorURI()
    {
        if (isset($this->attributes['actorURI']) && strlen($this->attributes['actorURI'])) {
            return base_url('api/actors/' . $this->attributes['actorURI']);
        }
    }

    public function getCharacterURI()
    {
        if (isset($this->attributes['characterURI']) && strlen($this->attributes['characterURI'])) {
            return base_url('api/characters/' . $this->attributes['characterURI']);
        }
    }
}
