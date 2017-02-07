<?php

namespace AppBundle\Model;

/**
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface SlotInterface
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
