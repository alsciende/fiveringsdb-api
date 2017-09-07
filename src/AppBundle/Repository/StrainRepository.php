<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of StrainRepository
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class StrainRepository extends EntityRepository
{
    /**
     * @param array $criteria
     */
    public function countBy(array $criteria)
    {
        $persister = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName);
        return $persister->count($criteria);
    }
}