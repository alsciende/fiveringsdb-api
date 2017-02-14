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

    /** @var \Symfony\Component\Serializer\Serializer */
    private $serializer;

    public function __construct (EntityManager $em)
    {
        $this->em = $em;

        $classMetadataFactory = new \Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory(new \Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader(new \Doctrine\Common\Annotations\AnnotationReader()));
        $normalizer = new \Symfony\Component\Serializer\Normalizer\ObjectNormalizer($classMetadataFactory);
        $this->serializer = new \Symfony\Component\Serializer\Serializer(array($normalizer));
    }

    /**
     * Transform an entity into an array
     * Associations are inlined as foreign_key => value
     * 
     * @param type $entity
     * 
     * @return array
     */
    public function normalize ($entity, $className)
    {
        $metadata = $this->em->getClassMetadata($className);

        $data = $this->serializer->normalize($entity, null, ['groups' => ['json']]);
        $this->normalizeAssociations($entity, $data, $metadata);
        
        return $data;
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

        $this->denormalizeAssociations($entity, $data, $metadata);

        return $entity;
    }

    public function getSingleIdentifier (\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $identifierFieldNames = $metadata->getIdentifierFieldNames();
        if(count($identifierFieldNames) > 1) {
            throw new InvalidArgumentException('Too many identifiers for ' . $metadata->getName());
        }
        return $identifierFieldNames[0];
    }

    public function normalizeAssociations ($entity, &$data, \Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        foreach($metadata->getAssociationMappings() as $mapping) {
            $target = $metadata->getFieldValue($entity, $mapping['fieldName']);
            $targetMetadata = $this->em->getClassMetadata($mapping['targetEntity']);
            $identifier = $this->getSingleIdentifier($targetMetadata);
            $value = $targetMetadata->getFieldValue($target, $identifier);
            $compositeField = $mapping['fieldName'] . '_' . $identifier;
            $data[$compositeField] = $value;
        }
    }

    public function denormalizeAssociations ($entity, $data, \Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        foreach($metadata->getAssociationMappings() as $mapping) {
            $qb = $this->em->createQueryBuilder();
            $qb->select($mapping['fieldName'])->from($mapping['targetEntity'], $mapping['fieldName']);

            $keys = [];
            foreach($mapping['joinColumns'] as $index => $joinColumn) {
                if(key_exists($joinColumn['name'], $data)) {
                    $keys[] = $key = $joinColumn['name'];
                    $value = $data[$key];
                    $condition = sprintf("%s.%s = ?%d", $mapping['fieldName'], $joinColumn['referencedColumnName'], $index);
                    $qb->andWhere($condition)->setParameter($index, $value);
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
        $identifier = $this->getSingleIdentifier($metadata);

        if(!isset($data[$identifier])) {
            throw new InvalidArgumentException('Missing identifier');
        }
        $id[$identifier] = $data[$identifier];

        $entity = $this->em->find($className, $id);
        if(!$entity) {
            $entity = new $className();
            $metadata->setFieldValue($entity, $identifier, $data[$identifier]);
        }

        unset($data[$identifier]);

        return $entity;
    }

}
