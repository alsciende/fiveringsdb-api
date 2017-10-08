<?php

namespace AppBundle\Service\DeckSearch;

use AppBundle\Entity\Deck;
use AppBundle\Search\DeckSearch;

/**
 * Description of AllTimePopularSearchService
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class AllTimePopularSearchService extends AbstractDeckSearchService
{
    static public function supports (): string
    {
        return 'fame';
    }

    public function search (DeckSearch $search)
    {
        $search->setTotal($this->getTotal());

        $dql = "SELECT d, u, COUNT(DISTINCT l.user) nbLikes, COUNT(DISTINCT c.id) nbComments
        FROM AppBundle:Deck d 
        JOIN d.user u 
        LEFT JOIN d.deckLikes l
        LEFT JOIN d.comments c
        WHERE d.published=:published 
        GROUP BY d, u
        ORDER BY nbLikes DESC, d.createdAt DESC";
        $query = $this->getEntityManager()
                      ->createQuery($dql)
                      ->setParameter('published', true)
                      ->setFirstResult($search->getFirstIndex())
                      ->setMaxResults($search->getLimit());

        foreach ($query->getResult() as $result) {
            /** @var Deck $deck */
            $deck = $result[0];
            $deck->setNbLikes((int) $result['nbLikes']);
            $deck->setNbComments((int) $result['nbComments']);
            $search->addRecord($deck);
        }
    }
}
