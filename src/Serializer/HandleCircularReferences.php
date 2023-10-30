<?php

namespace App\Serializer;

class HandleCircularReferences{

    public function __invoke($object)
    {
        return $object->getId();
    }
}