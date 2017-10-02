<?php

namespace AppBundle\Model;

use AppBundle\Entity\Card;
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

    public function filterByType (string $type): self
    {
        return $this->filter(
            function (CardSlotInterface $slot) use ($type) {
                return $slot->getCard()->getType() === $type;
            }
        );
    }

    public function filterBySide (string $side): self
    {
        return $this->filter(
            function (CardSlotInterface $slot) use ($side) {
                return $slot->getCard()->getSide() === $side;
            }
        );
    }

    public function filterByClan (string $clan = null): self
    {
        return $this->filter(
            function (CardSlotInterface $slot) use ($clan) {
                return $slot->getCard()->getClan() === $clan;
            }
        );
    }

    public function filterByElement (string $element = null): self
    {
        return $this->filter(
            function (CardSlotInterface $slot) use ($element) {
                return $slot->getCard()->getElement() === $element;
            }
        );
    }

    public function findStronghold (): ?Card
    {
        $slot = $this->find(
            function (CardSlotInterface $slot) {
                return $slot->getCard()->getType() === 'stronghold';
            }
        );

        if ($slot instanceof CardSlotInterface) {
            return $slot->getCard();
        }

        return null;
    }

    public function findRole (): ?Card
    {
        $slot = $this->find(
            function (CardSlotInterface $slot) {
                return $slot->getCard()->getType() === 'role';
            }
        );

        if ($slot instanceof CardSlotInterface) {
            return $slot->getCard();
        }

        return null;
    }

    public function findPrimaryClan (): ?string
    {
        $stronghold = $this->findStronghold();

        return $stronghold ? $stronghold->getClan() : null;
    }

    public function findSecondaryClan (string $primaryClan = null): ?string
    {
        if ($primaryClan === null) {
            return null;
        }

        foreach ($this->toArray() as $slot) {
            if ($slot->getCard()->getClan() !== 'neutral'
                && $slot->getCard()->getClan() !== $primaryClan) {
                return $slot->getCard()->getClan();
            }
        }

        return null;
    }

    /**
     * Return true if all quantities are 1
     */
    public function isDistinct (): bool
    {
        $alien = $this->find(
            function (CardSlotInterface $slot) {
                return $slot->getQuantity() > 1;
            }
        );

        return $alien === null;
    }
}
