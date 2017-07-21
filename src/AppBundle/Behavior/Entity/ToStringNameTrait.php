<?php

namespace AppBundle\Behavior\Entity;

trait ToStringNameTrait
{
    public function __toString()
    {
        return $this->name;
    }
}
