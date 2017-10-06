<?php

namespace AppBundle\Service\DeckSearch;

use AppBundle\Entity\Deck;
use AppBundle\Search\DeckSearch;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Description of DeckSearchInterface
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface DeckSearchServiceInterface
{
    /**
     * @return string
     */
    static public function supports(): string;

    /**
     * @param array $arguments
     */
    public function search(DeckSearch $search);
}