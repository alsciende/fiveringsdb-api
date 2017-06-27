<?php

namespace AppBundle\Service\DeckChecker;

use AppBundle\Entity\Card;
use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Model\CardSlotInterface;
use AppBundle\Service\DeckValidator;

/**
 * Description of ConflictDeckChecker
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ConflictDeckChecker implements DeckCheckerInterface
{
    public function check(CardSlotCollectionDecorator $deckCards): int
    {
        $conflictDeck = $deckCards->filterBySide(Card::SIDE_CONFLICT);
        $conflictCount = $conflictDeck->countElements();

        if ($conflictCount < 40) {
            return DeckValidator::TOO_FEW_CONFLICT;
        }

        if ($conflictCount > 45) {
            return DeckValidator::TOO_MANY_CONFLICT;
        }

        if($conflictDeck->filterByType(Card::TYPE_CHARACTER)->countElements() > 10) {
            return DeckValidator::TOO_MANY_CHARACTER_IN_CONFLICT;
        }

        $strongholdSlot = $deckCards->findStrongholdSlot();
        if ($strongholdSlot !== null) {
            /** @var Card $stronghold */
            $stronghold = $strongholdSlot->getElement();
            $clan = $stronghold->getClan();
            $influencePool = $stronghold->getInfluencePool();

            $offClanSlots = $deckCards->filter(function ($slot) use ($clan) {
                /** @var CardSlotInterface $slot */
                return $slot->getCard()->getClan() !== null
                    && $slot->getCard()->getClan() !== $clan;
            });

            foreach($offClanSlots as $slot) {
                /** @var CardSlotInterface $slot */
                $influencePool -= $slot->getQuantity() * $slot->getCard()->getInfluenceCost();
            }

            if($influencePool < 0) {
                return DeckValidator::NOT_ENOUGH_INFLUENCE;
            }

            if(count(
                array_unique(
                    array_map(
                        function ($slot) {
                            /** @var CardSlotInterface $slot */
                            return $slot->getCard()->getClan();
                        },
                        $offClanSlots->toArray()
                    )
                )
            ) > 1) {
                return DeckValidator::TOO_MANY_OFF_CLANS;
            }
        }

        return DeckValidator::VALID_DECK;
    }
}
