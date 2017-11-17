<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Activity;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Description of ActivityRepository
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ActivityRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param int $limit
     * @return Activity[]
     */
    public function findForUser(User $user, int $limit): array
    {
        $dql = "SELECT a, u1, d, u2
        FROM AppBundle:Activity a
        LEFT JOIN a.user u1
        LEFT JOIN a.deck d
        LEFT JOIN d.user u2
        WHERE EXISTS (SELECT n FROM AppBundle:Notification n WHERE n.user=:user AND n.activity=a)
        ORDER BY a.createdAt DESC";
        $query = $this->getEntityManager()
                      ->createQuery($dql)
                      ->setParameter('user', $user)
                      ->setFirstResult(0)
                      ->setMaxResults($limit);

        return $query->getResult();
    }
}