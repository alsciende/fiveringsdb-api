<?php

namespace AppBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class CardSlotCollectionDecorator extends AbstractSlotCollectionDecorator
{
    /**
     * @return CardSlotInterface[]
     */
    function toArray (): array
    {
        return parent::toArray();
    }

    function filterByType(string $type_code): self
    {
        return $this->filter(function ($element) use ($type_code) {
            /** @var $element CardSlotInterface */
            return $element->getCard()->getType() === $type_code;
        });
    }

    function filterBySide(string $side_code): self
    {
        return $this->filter(function ($element) use ($side_code) {
            /** @var $element CardSlotInterface */
            return $element->getCard()->getSide() === $side_code;
        });
    }

    function filterByClan(string $clan_code = null): self
    {
        return $this->filter(function ($element) use ($clan_code) {
            /** @var $element CardSlotInterface */
            return $element->getCard()->getClan() === $clan_code;
        });
    }

    function filterByElement(string $element_code = null): self
    {
        return $this->filter(function ($element) use ($element_code) {
            /** @var $element CardSlotInterface */
            return $element->getCard()->getElement() === $element_code;
        });
    }

    function findStrongholdSlot(): ?SlotInterface
    {
        return $this->find(function ($element) {
            /** @var $element CardSlotInterface */
            return $element->getCard()->getType() === 'stronghold';
        });
    }
}
