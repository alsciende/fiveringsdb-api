<?php

namespace AppBundle\Service\DeckChecker;

/**
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface DeckCheckerInterface
{
    /**
     * 
     * @param \AppBundle\Entity\Deck $deck
     * @return integer
     */
    public function check(\AppBundle\Entity\Deck $deck);
}
