<?php

namespace AppBundle\Model;

/**
 * Description of PackCardSlotCollectionDecorator
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackCardSlotCollectionDecorator extends AbstractSlotCollectionDecorator
{
    public function getQuantities (): array
    {
        $quantities = [];
        foreach ($this->toArray() as $slot) {
            $quantities[$slot->getPack()->getCode()] = $slot->getQuantity();
        }
        ksort($quantities);

        return $quantities;
    }
}
