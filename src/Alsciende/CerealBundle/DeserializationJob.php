<?php

namespace Alsciende\CerealBundle;

/**
 * Deserialization Job for exactly one entity
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeserializationJob
{

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /** @var AssociationNormalizer */
    private $normalizer;
    
    /** @var  \Symfony\Component\Validator\Validator\RecursiveValidator */
    private $validator;

    /* @var string */
    private $filepath;

    /* @var string */
    private $classname;

    /* @var \Doctrine\ORM\Mapping\ClassMetadata */
    private $metadata;

    /* @var object */
    private $entity;

    /* @var array */
    private $original;

    /* @var array */
    private $incoming;

    /* @var array */
    private $changes;

    /* @var array */
    private $renamedKeys;

    function __construct ($filepath, $incoming, $classname)
    {
        $this->filepath = $filepath;
        $this->incoming = $incoming;
        $this->classname = $classname;
    }

    function run (\Doctrine\ORM\EntityManager $em, \Symfony\Component\Validator\Validator\RecursiveValidator $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
        
        $this->normalizer = new AssociationNormalizer($em);

        $this->metadata = $this->em->getClassMetadata($this->classname);

        // find the entity based on the incoming identifier
        $this->entity = $this->findEntity();

        // normalize the entity in its original state
        $this->original = $this->normalizer->normalize($this->entity);

        // compute changes between the normalized data
        $this->changes = array_diff($this->incoming, $this->original);

        // denormalize the associations in the incoming data
        $this->denormalizeIncomingAssociations();

        // update the entity with the field updated in incoming
        foreach($this->changes as $field => $value) {
            if(isset($this->renamedKeys[$field])) {
                $field = $this->renamedKeys[$field];
                $value = $this->incoming[$field];
            }
            $this->metadata->setFieldValue($this->entity, $field, $value);
        }

        $errors = $this->validator->validate($this->entity);
        if(count($errors) > 0) {
            $errorsString = (string) $errors;
            throw new \Exception($errorsString);
        }

        $this->em->merge($this->entity);
    }

    /**
     * Finds all the foreign keys in $incoming and replaces them with
     * a proper Doctrine association
     * 
     * eg "article_id" => 2134 becomes "article" => (object Article)
     * 
     * @throws \Alsciende\CerealBundle\Exception\InvalidForeignKeyException
     */
    protected function denormalizeIncomingAssociations ()
    {
        $associations = [];

        foreach($this->metadata->getAssociationMappings() as $mapping) {
            $qb = $this->em->createQueryBuilder();
            $qb->select($mapping['fieldName'])->from($mapping['targetEntity'], $mapping['fieldName']);

            $keys = [];
            foreach($mapping['joinColumns'] as $index => $joinColumn) {
                if(key_exists($joinColumn['name'], $this->incoming)) {
                    $keys[] = $key = $joinColumn['name'];
                    $value = $this->incoming[$key];
                    $condition = sprintf("%s.%s = ?%d", $mapping['fieldName'], $joinColumn['referencedColumnName'], $index);
                    $qb->andWhere($condition)->setParameter($index, $value);
                } else {
                    continue 2; // next $mapping
                }
            }

            try {
                $result = $qb->getQuery()->getSingleResult();
            } catch(\Doctrine\ORM\NoResultException $ex) {
                throw new \Alsciende\CerealBundle\Exception\InvalidForeignKeyException($this->incoming, $keys, $this->classname);
            }

            $associations[$mapping['fieldName']] = $result;
            foreach($keys as $key) {
                $this->renamedKeys[$key] = $mapping['fieldName'];
                unset($this->incoming[$key]);
            }
        }

        $this->incoming = array_merge($this->incoming, $associations);
    }

    protected function findEntity ()
    {
        list($identifierField, $uniqueValue) = $this->getIdentifierPair($this->metadata);

        $entity = $this->em->find($this->classname, [$identifierField => $uniqueValue]);
        if(!$entity) {
            $classname = $this->classname;
            $entity = new $classname();
            $this->metadata->setFieldValue($entity, $identifierField, $uniqueValue);
        }

        return $entity;
    }
    
    
    function getIdentifierPair (\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $identifier = $this->normalizer->getSingleIdentifier($metadata);

        if(!isset($this->incoming[$identifier])) {
            throw new \InvalidArgumentException('Missing identifier');
        }

        return array($identifier, $this->incoming[$identifier]);
    }


    function getEntity ()
    {
        return $this->entity;
    }

    function getChanges ()
    {
        return $this->changes;
    }

    function getOriginal ()
    {
        return $this->original;
    }

}
