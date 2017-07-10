<?php

namespace AppBundle\Service\DeckCheck;

use AppBundle\Entity\Card;
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
    public function check(CardSlotCollectionDecorator $deckCards): int
    {
        $dynastyDeck = $deckCards->filterBySide('dynasty');
        $dynastyCount = $dynastyDeck->countCards();

        if ($dynastyCount < 40) {
            return DeckValidator::TOO_FEW_DYNASTY;
        }

        if ($dynastyCount > 45) {
            return DeckValidator::TOO_MANY_DYNASTY;
        }

        $strongholdSlot = $deckCards->findStrongholdSlot();
        if ($strongholdSlot !== null) {
            $stronghold = $strongholdSlot->getCard();
            $clan = $stronghold->getClan();

            $offClanSlot = $deckCards->find(function ($slot) use ($clan) {
                /** @var CardSlotInterface $slot */
                return $slot->getCard()->getClan() !== 'neutral'
                    && $slot->getCard()->getClan() !== $clan;
            });

            if($offClanSlot !== null) {
                return DeckValidator::OFF_CLAN_DYNASTY;
            }
        }

        return DeckValidator::VALID_DECK;
    }
}