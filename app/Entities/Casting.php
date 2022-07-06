<?php

namespace App\Entities;

class Casting extends APIEntity
{
    public function getActorURI()
    {
        return $this->getURIProperty('actorURI', 'actors');
    }

    public function getCharacterURI()
    {
        return $this->getURIProperty('characterURI', 'characters');
    }
}
