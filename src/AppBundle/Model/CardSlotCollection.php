<?php

namespace AppBundle\Model;

/**
 * Description of CardSlotCollection
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CardSlotCollection extends \Doctrine\Common\Collections\ArrayCollection
{
    function __construct (array $elements = array())
    {
        parent::__construct($elements);
    }

}
