<?php

namespace AppBundle\Service\DeckSearch;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Description of AbstractPopularDeckSearchService
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PopularDeckSearchService extends AbstractGenericDeckSearchService
{
    static public function supports (): string
    {
        return 'popularity';
    }

    public function getOrderedQuery (QueryBuilder $qb): Query
    {
        $qb
            ->orderBy('nbLikes', 'DESC')
            ->addOrderBy('d.publishedAt', 'DESC');

        return $qb->getQuery();
    }
}