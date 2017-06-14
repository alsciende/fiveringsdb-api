<?php

namespace AppBundle\Service\DeckChecker;

/**
 * Description of CardCountChecker
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CardCountChecker implements DeckCheckerInterface
{
    
    public function check(\AppBundle\Entity\Deck $deck)
    {
        $count = $deck->getDeckCards()->countElements();

        if ($count < 60) {
            return \AppBundle\Service\DeckChecker::TOO_FEW_CARDS;
        }

        if ($count > 60) {
            return \AppBundle\Service\DeckChecker::TOO_MANY_CARDS;
        }
    }

}
