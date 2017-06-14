<?php

namespace AppBundle\Service\DeckChecker;

/**
 * Description of CardCopiesChecker
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CardCopiesChecker implements DeckCheckerInterface
{
    
    public function check(\AppBundle\Entity\Deck $deck)
    {
        $slot = $deck->getDeckCards()->find(function (\AppBundle\Model\CardSlotInterface $slot) {
            /* @var $slot \AppBundle\Entity\DeckCard */
            return $slot->getQuantity() > 3;
        });

        if ($slot) {
            return \AppBundle\Service\DeckChecker::TOO_MANY_COPIES;
        }
    }

}
