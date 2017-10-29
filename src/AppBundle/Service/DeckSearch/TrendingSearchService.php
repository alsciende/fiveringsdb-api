<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 27/10/17
 * Time: 13:16
 */

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
        $dql = "SELECT COUNT(d)
        FROM AppBundle:Deck d
        WHERE d.published = :published
        AND d.freshness = 0";
        $query = $this->getEntityManager()
                      ->createQuery($dql)
                      ->setParameter('published', true);

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
            COUNT(DISTINCT c.user_id) nbComments, 
            SUM(s.score) 
        FROM (
	        SELECT l.deck_id, (86400 - UNIX_TIMESTAMP() + UNIX_TIMESTAMP(created_at)) score
	        FROM deck_likes l 
	        WHERE l.freshness = 0
	        UNION ALL
	        SELECT c.deck_id, (86400 - UNIX_TIMESTAMP() + UNIX_TIMESTAMP(created_at)) score
	        FROM comments c 
	        WHERE c.freshness = 0
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
                      ->setParameter(1, $search->getFirstIndex())
                      ->setParameter(2, $search->getLimit());

        foreach ($query->getResult() as $result) {
            /** @var Deck $deck */
            $deck = $result[0];
            $deck->setNbLikes((int) $result['nbLikes']);
            $deck->setNbComments((int) $result['nbComments']);
            $search->addRecord($deck);
        }
    }
}
