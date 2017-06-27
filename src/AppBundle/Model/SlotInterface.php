<?php

namespace AppBundle\Model;

/**
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface SlotInterface
{
    public function getElement (): SlotElementInterface;

    public function getQuantity (): int;
}
