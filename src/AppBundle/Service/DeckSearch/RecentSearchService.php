<?php

namespace AppBundle\Service\DeckSearch;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Description of RecentSearch
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class RecentSearchService extends AbstractGenericDeckSearchService
{
    static public function supports (): string
    {
        return 'date';
    }

    public function getOrderedQuery (QueryBuilder $qb): Query
    {
        $qb
            ->orderBy('d.publishedAt', 'DESC');

        return $qb->getQuery();
    }
}