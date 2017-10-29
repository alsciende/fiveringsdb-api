<?php

namespace AppBundle\Service\DeckSearch;

use AppBundle\Entity\Deck;
use AppBundle\Search\DeckSearch;

/**
 * Description of AbstractPopularDeckSearchService
 *
 * @author Alsciende <alsciende@icloud.com>
 */
abstract class AbstractPopularDeckSearchService extends AbstractDeckSearchService implements PopularDeckSearchServiceInterface
{
    public function search (DeckSearch $search)
    {
        $values = range(0, $this->getFreshness());

        $dql = "SELECT COUNT(d)
        FROM AppBundle:Deck d
        WHERE d.published = :published
        AND d.freshness IN (:freshness)";
        $query = $this->getEntityManager()
                      ->createQuery($dql)
                      ->setParameter('published', true)
                      ->setParameter('freshness', $values);

        $search->setTotal((int) $query->getSingleScalarResult());

        $dql = "SELECT d, u, COUNT(DISTINCT l.user) nbLikes, COUNT(DISTINCT c.id) nbComments
        FROM AppBundle:Deck d 
        JOIN d.user u
        LEFT JOIN d.deckLikes l
        LEFT JOIN d.comments c
        WHERE d.published = :published 
        AND d.freshness IN (:freshness)
        GROUP BY d, u
        ORDER BY nbLikes DESC, d.createdAt DESC";
        $query = $this->getEntityManager()
                      ->createQuery($dql)
                      ->setParameter('published', true)
                      ->setParameter('freshness', $values)
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