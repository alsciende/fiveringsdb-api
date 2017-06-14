<?php

namespace AppBundle\Model;

/**
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface SlotInterface
{
    
    /**
     * @return SlotElementInterface
     */
    public function getElement ();
    
    /**
     * @return integer
     */
    public function getQuantity ();
}
