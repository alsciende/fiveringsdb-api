<?php

namespace AppBundle\Service\DeckSearch;

use AppBundle\Entity\Deck;
use AppBundle\Search\DeckSearch;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 */
class TrendingSearchService extends AbstractDeckSearchService
{
    static public function supports (): string
    {
        return 'trending';
    }

    public function search (DeckSearch $search)
    {
        $date = new \DateTime();
        date_sub($date, new \DateInterval("P1D"));

        $dql = "SELECT COUNT(d)
        FROM AppBundle:Deck d
        WHERE d.published = :published
        AND d.createdAt >= :date";
        $query = $this->getEntityManager()
                      ->createQuery($dql)
                      ->setParameter('published', true)
                      ->setParameter('date', $date);

        $search->setTotal((int) $query->getSingleScalarResult());

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('AppBundle\Entity\Deck', 'd');
        $rsm->addJoinedEntityFromClassMetadata('AppBundle\Entity\User', 'u', 'd', 'user', ['id' => 'user_id']);
        $rsm->addScalarResult('nbLikes', 'nbLikes');
        $rsm->addScalarResult('nbComments', 'nbComments');

        $selectClause = $rsm->generateSelectClause();

        $sql = "SELECT 
            $selectClause, 
            COUNT(DISTINCT l.user_id) nbLikes, 
            COUNT(DISTINCT c.id) nbComments, 
            SUM(s.score) 
        FROM (
	        SELECT deck_id, (86400 - UNIX_TIMESTAMP() + UNIX_TIMESTAMP(created_at)) score
	        FROM deck_likes 
	        WHERE created_at >= ?
	        UNION ALL
	        SELECT deck_id, (86400 - UNIX_TIMESTAMP() + UNIX_TIMESTAMP(created_at)) score
	        FROM comments 
	        WHERE created_at >= ?
        ) s
        INNER JOIN decks d ON d.id = s.deck_id
        INNER JOIN users u ON d.user_id = u.id
        LEFT JOIN deck_likes l ON d.id = l.deck_id
        LEFT JOIN comments c ON d.id = c.deck_id
        WHERE d.published = 1 
        GROUP BY d.id
        ORDER BY SUM(s.score) DESC
        LIMIT ?, ?";

        $query = $this->getEntityManager()
                      ->createNativeQuery($sql, $rsm)
                      ->setParameter(1, $date->format('Y-m-d H:i:s'))
                      ->setParameter(2, $date->format('Y-m-d H:i:s'))
                      ->setParameter(3, $search->getFirstIndex())
                      ->setParameter(4, $search->getLimit());

        foreach ($query->getResult() as $result) {
            /** @var Deck $deck */
            $deck = $result[0];
            $deck->setNbLikes((int) $result['nbLikes']);
            $deck->setNbComments((int) $result['nbComments']);
            $search->addRecord($deck);
        }
    }
}
