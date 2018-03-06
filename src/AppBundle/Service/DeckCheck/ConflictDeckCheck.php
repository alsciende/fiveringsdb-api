<?php

namespace AppBundle\Service\DeckCheck;

use AppBundle\Entity\Card;
use AppBundle\Entity\Deck;
use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Model\CardSlotInterface;
use AppBundle\Service\DeckValidator;

/**
 * Description of ConflicDeckCheck
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ConflictDeckCheck implements DeckCheckInterface
{
    public function check (CardSlotCollectionDecorator $deckCards, string $format): int
    {
        $conflictDeck = $deckCards->filterBySide(Card::SIDE_CONFLICT);
        $conflictCount = $conflictDeck->countCards();

        $minCount = $format === Deck::FORMAT_SINGLE_CORE ? 30 : 40;
        if ($conflictCount < $minCount) {
            return DeckValidator::TOO_FEW_CONFLICT;
        }

        $maxCount = $format === Deck::FORMAT_SINGLE_CORE ? 30 : 45;
        if ($conflictCount > $maxCount) {
            return DeckValidator::TOO_MANY_CONFLICT;
        }

        if ($conflictDeck->filterByType(Card::TYPE_CHARACTER)->countCards() > 10) {
            return DeckValidator::TOO_MANY_CHARACTER_IN_CONFLICT;
        }

        $supportingClan = null;
        $stronghold = $deckCards->findStronghold();
        if ($stronghold instanceof Card) {
            $clan = $stronghold->getClan();
            $influencePool = $stronghold->getInfluencePool();

            $role = $deckCards->findRole();
            if ($role instanceof Card) {
                if ($role->hasTrait('keeper')) {
                    $influencePool += 3;
                } else {
                    foreach (Card::CLANS as $otherClan) {
                        if ($role->hasTrait($otherClan)) {
                            $supportingClan = $otherClan;
                            $influencePool += 8;
                        }
                    }
                }
            }

            $offClanSlots = $conflictDeck->filter(
                function (CardSlotInterface $slot) use ($clan) {
                    return $slot->getCard()->getClan() !== 'neutral'
                        && $slot->getCard()->getClan() !== $clan;
                }
            );

            foreach ($offClanSlots as $slot) {
                /** @var CardSlotInterface $slot */
                if ($slot->getCard()->getInfluenceCost() === null) {
                    return DeckValidator::FORBIDDEN_SPLASH;
                }
                if ($supportingClan !== null && $slot->getCard()->getClan() !== $supportingClan) {
                    return DeckValidator::FORBIDDEN_SPLASH;
                }

                $influencePool -= $slot->getQuantity() * $slot->getCard()->getInfluenceCost();
            }

            if ($influencePool < 0) {
                return DeckValidator::NOT_ENOUGH_INFLUENCE;
            }

            if (count(
                    array_unique(
                        array_map(
                            function (CardSlotInterface $slot) {
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
