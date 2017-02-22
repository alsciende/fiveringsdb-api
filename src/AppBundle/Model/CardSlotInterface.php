<?php

namespace AppBundle\Model;

/**
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface CardSlotInterface
{

    /**
     * @return AppBundle\Entity\Card
     */
    public function getCard ();

    /**
     * @return integer
     */
    public function getQuantity ();
}
