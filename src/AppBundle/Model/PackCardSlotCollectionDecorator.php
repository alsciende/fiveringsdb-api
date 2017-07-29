<?php

namespace AppBundle\Model;

use AppBundle\Entity\PackCard;

/**
 * Description of PackCardSlotCollectionDecorator
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackCardSlotCollectionDecorator extends CardSlotCollectionDecorator
{
    /**
     * @return PackCard[]
     */
    public function toArray (): array
    {
        return parent::toArray();
    }

    public function getQuantities (): array
    {
        $quantities = [];
        foreach ($this->toArray() as $slot) {
            $quantities[$slot->getPack()->getId()] = $slot->getQuantity();
        }
        ksort($quantities);

        return $quantities;
    }
}
