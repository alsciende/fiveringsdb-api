<?php

namespace AppBundle\Service\DeckSearch;

use AppBundle\Entity\Deck;
use AppBundle\Search\DeckSearch;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Description of RecentSearch
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class RecentSearchService extends AbstractDeckSearchService
{
    static public function supports (): string
    {
        return 'recent';
    }

    public function search (DeckSearch $search)
    {
        $dql = "SELECT COUNT(d)
        FROM AppBundle:Deck d 
        JOIN d.user u 
        WHERE d.published=:published 
        ORDER BY d.createdAt DESC";
        $query = $this->getEntityManager()
                      ->createQuery($dql)
                      ->setParameter('published', true);

        $search->setTotal((int) $query->getSingleScalarResult());

        $dql = "SELECT d, u, COUNT(l.user), COUNT(c.id)
        FROM AppBundle:Deck d 
        JOIN d.user u 
        LEFT JOIN d.deckLikes l
        LEFT JOIN d.comments c
        WHERE d.published=:published 
        GROUP BY d, u
        ORDER BY d.createdAt DESC";
        $query = $this->getEntityManager()
                      ->createQuery($dql)
                      ->setParameter('published', true)
                      ->setFirstResult($search->getFirstIndex())
                      ->setMaxResults($search->getLimit());

        foreach($query->getResult() as $result) {
            /** @var Deck $deck */
            $deck = $result[0];
            $deck->setNbLikes((int) $result[1]);
            $deck->setNbComments((int) $result[2]);
            $search->addRecord($deck);
        }
    }
}