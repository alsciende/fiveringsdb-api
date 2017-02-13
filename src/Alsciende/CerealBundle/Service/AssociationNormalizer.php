<?php

namespace Alsciende\CerealBundle\Service;

use Alsciende\CerealBundle\Exception\InvalidForeignKeyException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

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
        $this->serializer = new Serializer(
                array(new PropertyNormalizer()), array(new JsonEncoder())
        );
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

        $classMetadata = $this->em->getClassMetadata($className);
        $associations = [];
        
        foreach($classMetadata->getAssociationMappings() as $mapping) {
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
                }
            }

            try {
                $result = $qb->getQuery()->getSingleResult();
            } catch(NoResultException $ex) {
                throw new InvalidForeignKeyException($data, $keys);
            }

            $associations[$mapping['fieldName']] = $result;
        }
        
        $entity = $this->serializer->denormalize(array_merge($data, $associations), $className);

        return $entity;
    }

}
