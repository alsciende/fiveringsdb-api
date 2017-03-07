<?php

namespace Alsciende\DoctrineSerializerBundle\Manager\Entity;

/**
 * Description of ObjectManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ObjectManager implements \Alsciende\DoctrineSerializerBundle\Manager\ObjectManagerInterface
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
    function getDependingClassNames ($className)
    {
        $result = [];
        $classMetadata = $this->entityManager->getClassMetadata($className);
        foreach($classMetadata->getAssociationMappings() as $mapping) {
            if($mapping['isOwningSide']) {
                $result[$mapping['fieldName']] = $mapping['targetEntity'];
            }
        }
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    function getAllManagedClassNames ()
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
    function getSingleIdentifier ($className)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);
        $identifierFieldNames = $classMetadata->getIdentifierFieldNames();
        if(count($identifierFieldNames) > 1) {
            throw new InvalidArgumentException('Too many identifiers for ' . $metadata->getName());
        }
        return $identifierFieldNames[0];
    }

    /**
     * {@inheritDoc}
     */
    function flush ()
    {
        $this->entityManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    function updateObject ($entity, $update)
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
    function readObject ($entity, $field)
    {
        $classMetadata = $this->entityManager->getClassMetadata(get_class($entity));
        return $classMetadata->getFieldValue($entity, $field);
    }

    /**
     * {@inheritDoc}
     */
    function findObject ($className, $identifiers)
    {
        return $this->entityManager->find($className, $identifiers);
    }

    /**
     * {@inheritDoc}
     */
    function getIdentifierValues ($className, $data)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);

        $result = [];
        foreach($classMetadata->getIdentifierFieldNames() as $identifierFieldName) {
            $result[$identifierFieldName] = $this->getIdentifierValue($className, $data, $identifierFieldName);
        }
        return $result;
    }

    /**
     * Returns the unique value (scalar or object) used as identifier in $data
     * considered as a normalization of $className
     * 
     * @param string $className
     * @param array $data
     * @param string $identifierFieldName
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private function getIdentifierValue ($className, $data, $identifierFieldName)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);

        if(in_array($identifierFieldName, $classMetadata->getFieldNames())) {
            if(!isset($data[$identifierFieldName])) {
                throw new \InvalidArgumentException("Missing identifier for entity " . $className . " in data " . json_encode($data));
            }
            return $data[$identifierFieldName];
        } else {
            $associationMapping = $classMetadata->getAssociationMapping($identifierFieldName);
            $referenceMetadata = $this->findReferenceMetadata($data, $associationMapping);
            $entity = $this->findReferencedEntity($identifierFieldName, $referenceMetadata);
            if(!$entity) {
                throw new \InvalidArgumentException("Cannot find entity referenced by $identifierFieldName in data " . json_encode($data));
            }
            return $entity;
        }
    }

    /**
     * {@inheritDoc}
     */
    function findForeignKeyValues ($className, $data)
    {
        $references = $this->findReferences($className, $data);

        $result = [];
        foreach($references as $field => $reference) {
            $entity = $this->findReferencedEntity($field, $reference);
            if(!$entity) {
                throw new \InvalidArgumentException("Invalid reference " . json_encode($reference));
            }
            $result[] = [
                "foreignKey" => $field,
                "foreignValue" => $entity,
                "joinColumns" => array_keys($reference['joinColumns'])
            ];
        }
        return $result;
    }

    /**
     * Return an array listing the associations in $metadata that exist in $data,
     * with the relevant information to find the referenced entity
     * 
     * @param string className
     * @param array $data
     * @return array
     */
    private function findReferences ($className, $data)
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
     * Finds the entity described by $reference. $field is a unique identifier.
     * 
     * @param type $field
     * @param type $reference
     * @return object
     */
    private function findReferencedEntity ($field, $reference)
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
