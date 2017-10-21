<?php

namespace AppBundle\Service\DeckSearch;

use AppBundle\Entity\Deck;
use AppBundle\Search\DeckSearch;

/**
 * Description of FeaturedDeckSearchService
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class FeaturedDeckSearchService extends AbstractDeckSearchService
{
    static public function supports (): string
    {
        return 'features';
    }

    public function search (DeckSearch $search)
    {
        $dql = "SELECT COUNT(f)
        FROM AppBundle:Feature f";
        $query = $this->getEntityManager()
                      ->createQuery($dql);

        $search->setTotal((int) $query->getSingleScalarResult());

        $dql = "SELECT d, u, COUNT(DISTINCT l.user), COUNT(DISTINCT c.id)
        FROM AppBundle:Deck d
        JOIN d.user u 
        LEFT JOIN d.deckLikes l
        LEFT JOIN d.comments c
        WHERE EXISTS(SELECT f FROM AppBundle:Feature f WHERE f.deck=d)
        GROUP BY d, u
        ORDER BY d.createdAt DESC";
        $query = $this->getEntityManager()
                      ->createQuery($dql)
                      ->setFirstResult($search->getFirstIndex())
                      ->setMaxResults($search->getLimit());

        foreach ($query->getResult() as $result) {
            /** @var Deck $deck */
            $deck = $result[0];
            $deck->setNbLikes((int) $result[1]);
            $deck->setNbComments((int) $result[2]);
            $search->addRecord($deck);
        }
    }
}