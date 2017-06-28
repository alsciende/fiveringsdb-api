<?php

namespace AppBundle\Service\DeckChecker;

use AppBundle\Model\CardSlotCollectionDecorator;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
interface DeckCheckerInterface
{
    public function check(CardSlotCollectionDecorator $deckCards): int;
}
