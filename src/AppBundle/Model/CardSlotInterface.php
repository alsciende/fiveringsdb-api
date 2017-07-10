<?php

namespace AppBundle\Model;

use AppBundle\Entity\Card;

/**
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface CardSlotInterface
{
    public function getCard (): Card;

    public function getQuantity (): int;
}
