<?php

namespace Alsciende\DoctrineSerializerBundle\Manager\Entity;

/**
 * Description of ReferenceManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ReferenceManager implements \Alsciende\DoctrineSerializerBundle\Manager\ReferenceManagerInterface
{

    /* @var \Doctrine\ORM\EntityManager */
    private $entityManager;
    
    function __construct (\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    function getClassDependencies($className)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);
        $result = [];
        foreach ($classMetadata->getAssociationMappings() as $mapping) {
            if($mapping['isOwningSide']) {
                $result[] = $mapping['targetEntity'];
            }
        }
        return $result;
    }
}
