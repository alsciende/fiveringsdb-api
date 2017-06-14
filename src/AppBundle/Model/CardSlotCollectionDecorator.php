<?php

namespace AppBundle\Model;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class CardSlotCollectionDecorator extends AbstractSlotCollectionDecorator
{

    /**
     * @return CardSlotInterface[]
     */
    function toArray ()
    {
        return parent::toArray();
    }

}
