<?php

namespace AppBundle\Model;

/**
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface CardSlotInterface extends SlotInterface
{

    /**
     * @return AppBundle\Entity\Card
     */
    public function getCard ();

}
