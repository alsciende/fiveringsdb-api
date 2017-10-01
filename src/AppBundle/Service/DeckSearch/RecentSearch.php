<?php

namespace AppBundle\Service\DeckSearch;

use AppBundle\Entity\Deck;
use AppBundle\Model\DeckSearch;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Description of RecentSearch
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class RecentSearch extends AbstractDeckSearch
{
    static public function supports (): string
    {
        return 'recent';
    }

    public function search (DeckSearch $search): Paginator
    {
        $dql = "SELECT d, u FROM AppBundle:Deck d JOIN d.user u WHERE d.published=:published ORDER BY d.createdAt DESC";
        $query = $this->getEntityManager()
                      ->createQuery($dql)
                      ->setParameter('published', true)
                      ->setFirstResult(0)
                      ->setMaxResults($search->getPagesize());

        return new Paginator($query, false);
    }
}