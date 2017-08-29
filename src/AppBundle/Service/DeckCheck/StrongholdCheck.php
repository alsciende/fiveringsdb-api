<?php

namespace AppBundle\Service\DeckCheck;

use AppBundle\Entity\Card;
use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Service\DeckValidator;

/**
 * Description of StrongholdChecker
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class StrongholdCheck implements DeckCheckInterface
{
    public function check (CardSlotCollectionDecorator $deckCards, string $format): int
    {
        $strongholdCount = $deckCards->filterByType(Card::TYPE_STRONGHOLD)->countCards();

        if ($strongholdCount < 1) {
            return DeckValidator::TOO_FEW_STRONGHOLD;
        }

        if ($strongholdCount > 1) {
            return DeckValidator::TOO_MANY_STRONGHOLD;
        }

        return DeckValidator::VALID_DECK;
    }
}