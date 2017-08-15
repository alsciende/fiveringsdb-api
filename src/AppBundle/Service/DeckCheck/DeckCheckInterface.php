<?php

namespace AppBundle\Service\DeckCheck;

use AppBundle\Model\CardSlotCollectionDecorator;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
interface DeckCheckInterface
{
    public function check(CardSlotCollectionDecorator $deckCards, string $format): int;
}
