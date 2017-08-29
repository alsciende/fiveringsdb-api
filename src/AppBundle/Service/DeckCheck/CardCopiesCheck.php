<?php

namespace AppBundle\Service\DeckCheck;

use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Model\CardSlotInterface;
use AppBundle\Service\DeckValidator;

/**
 * Description of CardCopiesChecker
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CardCopiesCheck implements DeckCheckInterface
{
    public function check (CardSlotCollectionDecorator $deckCards, string $format): int
    {
        $slot = $deckCards->find(
            function (CardSlotInterface $slot) {
                return $slot->getQuantity() > $slot->getCard()->getDeckLimit();
            }
        );

        if ($slot) {
            return DeckValidator::TOO_MANY_COPIES;
        }

        return DeckValidator::VALID_DECK;
    }
}
