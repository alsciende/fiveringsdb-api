<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of PackRepository
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackRepository extends EntityRepository
{
    public function findAllSorted ()
    {
        return $this->findBy([], ['ffgId' => 'ASC', 'releasedAt' => 'ASC']);
    }
}