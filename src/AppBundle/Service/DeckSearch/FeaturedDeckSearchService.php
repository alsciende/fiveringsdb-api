<?php

namespace AppBundle\Service\DeckSearch;

use AppBundle\Entity\Deck;
use AppBundle\Entity\Feature;
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

        $dql = "SELECT f, d, u, COUNT(DISTINCT l.user), COUNT(DISTINCT c.id)
        FROM AppBundle:Feature f
        JOIN f.deck d
        JOIN d.user u 
        LEFT JOIN d.deckLikes l
        LEFT JOIN d.comments c
        GROUP BY d, u
        ORDER BY d.createdAt DESC";
        $query = $this->getEntityManager()
                      ->createQuery($dql)
                      ->setFirstResult($search->getFirstIndex())
                      ->setMaxResults($search->getLimit());

        foreach ($query->getResult() as $result) {
            /** @var Feature $feature */
            $feature = $result[0];
            $deck = $feature->getDeck();
            $deck->setNbLikes((int) $result[1]);
            $deck->setNbComments((int) $result[2]);
            $search->addRecord($deck);
        }
    }
}