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
    function getDependingClassNames($className)
    {
        $result = [];
        $classMetadata = $this->entityManager->getClassMetadata($className);
        foreach ($classMetadata->getAssociationMappings() as $mapping) {
            if($mapping['isOwningSide']) {
                $result[] = $mapping['targetEntity'];
            }
        }
        return $result;
    }
    
    /**
     * {@inheritDoc}
     */
    function getAllManagedClassNames()
    {
        $result = [];
        $allMetadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        foreach($allMetadata as $metadata) {
            $result[] = $metadata->getName();
        }
        return $result;
    }
    
    /**
     * {@inheritDoc}
     */
    function flush()
    {
        $this->entityManager->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    function updateEntity($entity, $update)
    {
        $classMetadata = $this->entityManager->getClassMetadata(get_class($entity));
        foreach($update as $field => $value) {
            $classMetadata->setFieldValue($entity, $field, $value);
        }
        $this->entityManager->merge($entity);
    }
    
    /**
     * {@inheritDoc}
     */
    function findEntity ($className, $data)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);

        $identifierPairs = $this->getIdentifierPairs($className, $data);

        $entity = $this->entityManager->find($className, $identifierPairs);

        if (!isset($entity)) {
            $classname = $className;
            $entity = new $classname();
            foreach ($identifierPairs as $identifierField => $uniqueValue) {
                $classMetadata->setFieldValue($entity, $identifierField, $uniqueValue);
            }
        }

        return $entity;
    }

    /**
     * Returns the array of identifier keys/values that can be used with find()
     * to find the entity described by $incoming
     * 
     * If an identifier is a foreignIdentifier, find the foreign entity
     * 
     * @return array
     * @throws \InvalidArgumentException
     */
    private function getIdentifierPairs ($className, $data)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);

        $pairs = [];

        $identifierFieldNames = $classMetadata->getIdentifierFieldNames();
        foreach ($identifierFieldNames as $identifierFieldName) {
            $pairs[$identifierFieldName] = $this->getIdentifierValue($className, $data, $identifierFieldName);
        }

        return $pairs;
    }

    private function getIdentifierValue ($className, $data, $identifierFieldName)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);

        if (in_array($identifierFieldName, $classMetadata->getFieldNames())) {
            if (!isset($data[$identifierFieldName])) {
                throw new \InvalidArgumentException("Missing identifier for entity " . $className . " in data " . json_encode($data));
            }
            return $data[$identifierFieldName];
        } else {
            $associationMapping = $classMetadata->getAssociationMapping($identifierFieldName);
            $referenceMetadata = $this->findReferenceMetadata($data, $associationMapping);
            $entity = $this->findReferencedEntity($identifierFieldName, $referenceMetadata);
            if (!$entity) {
                throw new \InvalidArgumentException("Cannot find entity referenced by $identifierFieldName in data " . json_encode($data));
            }
            return $entity;
        }
    }
    
    /**
     * {@inheritDoc}
     */
    function findReferences ($className, $data)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);

        $references = [];
        foreach($classMetadata->getAssociationMappings() as $mapping) {
            $reference = $this->findReferenceMetadata($data, $mapping);
            if($reference) {
                $references[$mapping['fieldName']] = $reference;
            }
        }
        return $references;
    }

    /**
     * Returns a description of the association, including the foreign key value
     * as found in $data
     * 
     * @param type $data an array where the value of the foreign key can be found
     * @param type $associationMapping
     * @return array
     */
    private function findReferenceMetadata ($data, $associationMapping)
    {
        if(!$associationMapping['isOwningSide']) {
            return;
        }
        $reference = [
            'joinColumns' => [],
            'className' => $associationMapping['targetEntity']
        ];
        foreach($associationMapping['sourceToTargetKeyColumns'] as $foreignKey => $referencedColumnName) {
            if(!key_exists($foreignKey, $data)) {
                return;
            }
            $reference['joinColumns'][$foreignKey] = [
                'referencedColumnName' => $referencedColumnName,
                'referencedValue' => $data[$foreignKey]
            ];
        }
        return $reference;
    }

    /**
     * {@inheritDoc}
     */
    function findReferencedEntity ($field, $reference)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select($field)->from($reference['className'], $field);
        foreach($reference['joinColumns'] as $foreignKey => $condition) {
            $conditionString = sprintf("%s.%s = :%s", $field, $condition['referencedColumnName'], $foreignKey);
            $qb->andWhere($conditionString)->setParameter($foreignKey, $condition['referencedValue']);
        }

        try {
            return $qb->getQuery()->getSingleResult();
        } catch(\Doctrine\ORM\NoResultException $ex) {
            throw new \InvalidArgumentException("Foreign key cannot be matched to a record");
        }
    }

}
