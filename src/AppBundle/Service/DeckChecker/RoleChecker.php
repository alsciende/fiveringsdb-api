<?php

namespace AppBundle\Service\DeckChecker;

use AppBundle\Entity\Card;
use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Service\DeckValidator;

/**
 * Description of RoleCardChecker
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class RoleChecker implements DeckCheckerInterface
{
    public function check(CardSlotCollectionDecorator $deckCards): int
    {
        if($deckCards->filterByType(Card::TYPE_ROLE)->countElements() > 1) {
            return DeckValidator::TOO_MANY_ROLE;
        }

        return DeckValidator::VALID_DECK;
    }
}