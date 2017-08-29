<?php

namespace AppBundle\Service\DeckCheck;

use AppBundle\Entity\Card;
use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Service\DeckValidator;

/**
 * Description of RoleCardChecker
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class RoleCheck implements DeckCheckInterface
{
    public function check (CardSlotCollectionDecorator $deckCards, string $format): int
    {
        if ($deckCards->filterByType(Card::TYPE_ROLE)->countCards() > 1) {
            return DeckValidator::TOO_MANY_ROLE;
        }

        return DeckValidator::VALID_DECK;
    }
}