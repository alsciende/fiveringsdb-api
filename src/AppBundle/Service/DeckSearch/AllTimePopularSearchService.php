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
        $sql = "SELECT DISTINCT a.nbLikes FROM (
                    SELECT d.id, COUNT(DISTINCT l.user_id) nbLikes
                    FROM decks d
                    LEFT JOIN deck_likes l ON l.deck_id=d.id
                    WHERE d.published = 1
                    GROUP BY d.id
                ) a ORDER BY a.nbLikes DESC";
        $statement = $this->getEntityManager()->getConnection()->prepare($sql);
        $statement->execute();
        $nbLikes = array_map(function ($row) {
            return $row['nbLikes'];
        }, $statement->fetchAll());

        $dql = "SELECT d, u, COUNT(DISTINCT l.user) nbLikes, COUNT(DISTINCT c.id) nbComments
        FROM AppBundle:Deck d 
        JOIN d.user u 
        LEFT JOIN d.deckLikes l
        LEFT JOIN d.comments c
        WHERE d.published=:published 
        GROUP BY d, u
        HAVING nbLikes >= :decile
        ORDER BY nbLikes DESC, d.createdAt DESC";
        $query = $this->getEntityManager()
                      ->createQuery($dql)
                      ->setParameter('published', true)
                      ->setParameter('decile', $nbLikes[intdiv(count($nbLikes), 10)])
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
