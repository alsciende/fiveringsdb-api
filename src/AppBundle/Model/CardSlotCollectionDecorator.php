<?php

namespace AppBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class CardSlotCollectionDecorator extends ArrayCollection
{
    public function __construct (array $cards = [])
    {
        parent::__construct($cards);
    }

    /**
     * @return CardSlotInterface[]
     */
    public function toArray (): array
    {
        return parent::toArray();
    }

    /**
     * Iterates over elements of the collection, returning the first element $p returns thruthly for.
     * The predicate is invoked with three arguments: ($value, $index|$key, $collection).
     */
    public function find (\Closure $p): ?CardSlotInterface
    {
        foreach ($this as $key => $slot) {
            if (call_user_func($p, $slot, $key, $this)) {
                return $slot;
            }
        }

        return null;
    }

    public function countCards (): int
    {
        $count = 0;
        foreach ($this->toArray() as $slot) {
            $count += $slot->getQuantity();
        }

        return $count;
    }

    public function getContent (): array
    {
        $content = [];
        foreach ($this->toArray() as $slot) {
            $content[$slot->getCard()->getId()] = $slot->getQuantity();
        }
        ksort($content);

        return $content;
    }

    function filterByType(string $type): self
    {
        return $this->filter(function ($slot) use ($type) {
            /** @var $slot CardSlotInterface */
            return $slot->getCard()->getType() === $type;
        });
    }

    function filterBySide(string $side): self
    {
        return $this->filter(function ($slot) use ($side) {
            /** @var $slot CardSlotInterface */
            return $slot->getCard()->getSide() === $side;
        });
    }

    function filterByClan(string $clan = null): self
    {
        return $this->filter(function ($slot) use ($clan) {
            /** @var $slot CardSlotInterface */
            return $slot->getCard()->getClan() === $clan;
        });
    }

    function filterByElement(string $element = null): self
    {
        return $this->filter(function ($slot) use ($element) {
            /** @var $slot CardSlotInterface */
            return $slot->getCard()->getElement() === $element;
        });
    }

    function findStrongholdSlot(): ?CardSlotInterface
    {
        return $this->find(function ($slot) {
            /** @var $slot CardSlotInterface */
            return $slot->getCard()->getType() === 'stronghold';
        });
    }
}
