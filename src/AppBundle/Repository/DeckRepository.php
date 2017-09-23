<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckRepository extends EntityRepository
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
