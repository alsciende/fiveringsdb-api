<?php

namespace AppBundle\Service\DeckSearch;

use AppBundle\Entity\Deck;
use AppBundle\Model\DeckSearch;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Description of DeckSearchInterface
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface DeckSearchInterface
{
    /**
     * @return string
     */
    static public function supports(): string;

    /**
     * @param array $arguments
     * @return Deck[]|Paginator
     */
    public function search(DeckSearch $search): Paginator;
}