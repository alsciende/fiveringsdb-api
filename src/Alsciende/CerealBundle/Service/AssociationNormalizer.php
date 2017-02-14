<?php

namespace Alsciende\CerealBundle\Service;

use Alsciende\CerealBundle\Exception\InvalidForeignKeyException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;

/**
 * Normalizer service that normalizes associations as ${field}_${referencedColumnName} => ${referencedValue}
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class AssociationNormalizer
{

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct (EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transform an array into an object of given class
     * Associations are fetched from the database
     * 
     * @param array $data
     * @param string $className
     * 
     * @return object
     * 
     * @throw \Alsciende\CerealBundle\Exception\InvalidForeignKeyException
     */
    public function denormalize ($data, $className)
    {

        $metadata = $this->em->getClassMetadata($className);

        $entity = $this->findOrCreate($data, $metadata);

        foreach($metadata->getFieldNames() as $field) {
            if(isset($data[$field])) {
                $metadata->setFieldValue($entity, $field, $data[$field]);
            }
        }

        $this->setAssociations($entity, $data, $metadata);

        return $entity;
    }

    public function setAssociations ($entity, $data, \Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        foreach($metadata->getAssociationMappings() as $mapping) {
            $qb = $this->em->createQueryBuilder();
            $qb->select($mapping['fieldName'])
                    ->from($mapping['targetEntity'], $mapping['fieldName']);

            $keys = [];
            foreach($mapping['joinColumns'] as $index => $joinColumn) {
                if(key_exists($joinColumn['name'], $data)) {
                    $keys[] = $key = $joinColumn['name'];
                    $value = $data[$key];
                    $condition = sprintf("%s.%s = ?%d", $mapping['fieldName'], $joinColumn['referencedColumnName'], $index);
                    $qb->andWhere($condition)
                            ->setParameter($index, $value);
                } else {
                    continue 2; // next $mapping
                }
            }

            try {
                $result = $qb->getQuery()->getSingleResult();
            } catch(NoResultException $ex) {
                throw new InvalidForeignKeyException($data, $keys, $metadata->getName());
            }

            $metadata->setFieldValue($entity, $mapping['fieldName'], $result);
        }
    }

    public function findOrCreate (&$data, \Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $id = [];
        $className = $metadata->getName();

        foreach($metadata->getIdentifierFieldNames() as $identifier) {
            if(!isset($data[$identifier])) {
                throw new InvalidArgumentException('Missing identifier');
            }
            $id[$identifier] = $data[$identifier];
        }

        $entity = $this->em->find($className, $id);
        if(!$entity) {
            $entity = new $className();
            foreach($metadata->getIdentifierFieldNames() as $identifier) {
                $metadata->setFieldValue($entity, $identifier, $data[$identifier]);
            }
        }

        foreach($metadata->getIdentifierFieldNames() as $identifier) {
            unset($data[$identifier]);
        }

        return $entity;
    }

}
