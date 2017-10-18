<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Feature;
use Doctrine\ORM\EntityRepository;

/**
 * Description of FeatureRepository
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class FeatureRepository extends EntityRepository
{
    public function findLastDate (): ?\DateTime
    {
        $mostRecent = $this->findOneBy([], ['date' => 'DESC']);
        if ($mostRecent instanceof Feature) {
            return $mostRecent->getDate();
        }

        return null;
    }
}