<?php

namespace AppBundle\Service\DeckCheck;

use AppBundle\Entity\Card;
use AppBundle\Entity\Deck;
use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Model\CardSlotInterface;
use AppBundle\Service\DeckValidator;

/**
 * Description of DynastyDeckCheck
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DynastyDeckCheck implements DeckCheckInterface
{
    public function check(CardSlotCollectionDecorator $deckCards, string $format): int
    {
        $dynastyDeck = $deckCards->filterBySide('dynasty');
        $dynastyCount = $dynastyDeck->countCards();

        $minCount = $format === Deck::FORMAT_SINGLE_CORE ? 30 : 40;
        if ($dynastyCount < $minCount) {
            return DeckValidator::TOO_FEW_DYNASTY;
        }

        $maxCount = $format === Deck::FORMAT_SINGLE_CORE ? 30 : 45;
        if ($dynastyCount > $maxCount) {
            return DeckValidator::TOO_MANY_DYNASTY;
        }

        $stronghold = $deckCards->findStronghold();
        if ($stronghold instanceof Card) {
            $clan = $stronghold->getClan();

            $offClanSlot = $deckCards->find(function ($slot) use ($clan) {
                /** @var CardSlotInterface $slot */
                return $slot->getCard()->getSide() === 'dynasty'
                    && $slot->getCard()->getClan() !== 'neutral'
                    && $slot->getCard()->getClan() !== $clan;
            });

            if($offClanSlot !== null) {
                return DeckValidator::OFF_CLAN_DYNASTY;
            }
        }

        return DeckValidator::VALID_DECK;
    }
}