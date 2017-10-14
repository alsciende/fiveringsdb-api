<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Deck;
use Doctrine\ORM\EntityRepository;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckRepository extends EntityRepository
{
    /**
     * @param array $criteria
     */
    public function countBy (array $criteria)
    {
        $persister = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName);

        return $persister->count($criteria);
    }

    /**
     * @param string $clan
     * @param int $period
     * @return Deck|null
     */
    public function findBestDeckForClan (string $clan, int $period): ?Deck
    {
        $dql = "SELECT d, COUNT(DISTINCT l.user) nbLikes
        FROM AppBundle:Deck d 
        LEFT JOIN d.deckLikes l
        WHERE d.published=:published 
        AND DATEDIFF(NOW(), d.createdAt) <= :days
        AND d.primaryClan=:clan
        GROUP BY d
        ORDER BY nbLikes DESC, d.createdAt DESC";
        $query = $this->getEntityManager()
                      ->createQuery($dql)
                      ->setParameter('published', true)
                      ->setParameter('days', $period)
                      ->setParameter('clan', $clan)
                      ->setFirstResult(0)
                      ->setMaxResults(1);

        $result = $query->getOneOrNullResult();

        if ($result === null) {
            return null;
        }

        return $result[0];
    }

    /**
     * @return string[]
     */
    public function findClans ()
    {
        return array_map(function ($item) {
            return $item['primaryClan'];
        }, $this
            ->getEntityManager()
            ->createQuery("SELECT DISTINCT d.primaryClan FROM AppBundle:Deck d WHERE d.primaryClan IS NOT NULL ORDER BY d.primaryClan")
            ->getArrayResult()
        );
    }
}
