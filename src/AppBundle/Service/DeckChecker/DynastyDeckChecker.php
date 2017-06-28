<?php

namespace AppBundle\Service\DeckChecker;

use AppBundle\Entity\Card;
use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Model\CardSlotInterface;
use AppBundle\Service\DeckValidator;

/**
 * Description of DynastyDeckChecker
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DynastyDeckChecker implements DeckCheckerInterface
{
    public function check(CardSlotCollectionDecorator $deckCards): int
    {
        $dynastyDeck = $deckCards->filterBySide('conflict');
        $dynastyCount = $dynastyDeck->countElements();

        if ($dynastyCount < 40) {
            return DeckValidator::TOO_FEW_DYNASTY;
        }

        if ($dynastyCount > 45) {
            return DeckValidator::TOO_MANY_DYNASTY;
        }

        $strongholdSlot = $deckCards->findStrongholdSlot();
        if ($strongholdSlot !== null) {
            /** @var Card $stronghold */
            $stronghold = $strongholdSlot->getElement();
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