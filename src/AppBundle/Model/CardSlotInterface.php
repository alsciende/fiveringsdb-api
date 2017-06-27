<?php

namespace AppBundle\Model;

use AppBundle\Entity\Card;

/**
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface CardSlotInterface extends SlotInterface
{
    public function getCard (): Card;
}
