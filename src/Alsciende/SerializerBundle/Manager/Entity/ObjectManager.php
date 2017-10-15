<?php

namespace Alsciende\SerializerBundle\Manager\Entity;

use Alsciende\SerializerBundle\Manager\BaseObjectManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of ObjectManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ObjectManager extends BaseObjectManager
{
    /* @var \Doctrine\ORM\EntityManager */

    private $entityManager;

    function __construct (EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    function getAllTargetClasses ($className)
    {
        $result = [];
        $classMetadata = $this->entityManager->getClassMetadata($className);
        foreach ($classMetadata->getAssociationMappings() as $mapping) {
            if ($mapping['isOwningSide']) {
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
        foreach ($allMetadata as $metadata) {
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
        if (count($identifierFieldNames) > 1) {
            throw new \InvalidArgumentException('Too many identifiers for ' . $classMetadata->getName());
        }
        return $identifierFieldNames[0];
    }

    /**
     * {@inheritDoc}
     */
    function updateObject ($entity, $data)
    {
        $className = $this->getClassName($entity);
        $classMetadata = $this->entityManager->getClassMetadata($className);
        foreach ($data as $field => $value) {
            $classMetadata->setFieldValue($entity, $field, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    function mergeObject ($entity)
    {
        return $this->entityManager->merge($entity);
    }

    function getFieldValue ($data, $className, $fieldName)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);
        $columnName = $classMetadata->getColumnName($fieldName);
        return $data[$columnName];
    }

    function setFieldValue (&$result, $className, $fieldName, $value)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);
        $columnName = $classMetadata->getColumnName($fieldName);
        $result[$columnName] = $value;
    }

    function getAssociationValue ($data, $className, $fieldName)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);
        $associationMapping = $classMetadata->getAssociationMapping($fieldName);
        $association = $this->findAssociation($data, $associationMapping);
        if ($association) {
            return $association['associationValue'];
        }
    }

    function setAssociationValue (&$result, $className, $fieldName, $value)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);
        $associationMapping = $classMetadata->getAssociationMapping($fieldName);
        list($referenceKey, $referenceValue) = $this->getReferenceFromAssociation($associationMapping['targetEntity'], $fieldName, $value);
        $result[$referenceKey] = $referenceValue;
    }

    /**
     * {@inheritDoc}
     */
    function readObject ($entity, $field)
    {
        $className = $this->getClassName($entity);
        $classMetadata = $this->entityManager->getClassMetadata($className);
        return $classMetadata->getFieldValue($entity, $field);
    }

    /**
     * {@inheritDoc}
     */
    function findObject ($identifiers, $className)
    {
        return $this->entityManager->find($className, $identifiers);
    }

    /**
     * {@inheritDoc}
     */
    function getIdentifierValues ($data, $className)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);

        $result = [];
        foreach ($classMetadata->getIdentifierFieldNames() as $identifierFieldName) {
            $result[$identifierFieldName] = $this->getIdentifierValue($data, $className, $identifierFieldName);
        }
        return $result;
    }

    /**
     * Returns the unique value (scalar or object) used as identifier in $data
     * considered as a normalization of $className
     *
     * @param array $data
     * @param string $className
     * @param string $identifierFieldName
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private function getIdentifierValue ($data, $className, $identifierFieldName)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);

        if (in_array($identifierFieldName, $classMetadata->getFieldNames())) {
            if (!isset($data[$identifierFieldName])) {
                throw new \InvalidArgumentException("Missing identifier for entity " . $className . " in data " . json_encode($data));
            }
            return $data[$identifierFieldName];
        } else {
            $associationMapping = $classMetadata->getAssociationMapping($identifierFieldName);
            $association = $this->findAssociation($data, $associationMapping);
            if (!$association || !isset($association['associationValue'])) {
                throw new \InvalidArgumentException("Cannot find entity referenced by $identifierFieldName in data " . json_encode($data));
            }
            return $association['associationValue'];
        }
    }

    /**
     * {@inheritDoc}
     */
    function findAssociations ($data, $className)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);

        $associations = [];
        foreach ($classMetadata->getAssociationMappings() as $mapping) {
            $association = $this->findAssociation($data, $mapping);
            if ($association) {
                $associations[] = $association;
            }
        }

        return $associations;
    }

    /**
     * Returns a description of the association, including the foreign key value
     * as found in $data
     *
     * @param array $data an array where the value of the foreign key can be found
     * @param array $associationMapping
     * @return array|null
     */
    private function findAssociation ($data, $associationMapping)
    {
        if (!$associationMapping['isOwningSide']) {
            return null;
        }
        $referenceKeys = [];
        $referenceValues = [];
        foreach ($associationMapping['sourceToTargetKeyColumns'] as $referenceKey => $targetIdentifier) {
            if (!key_exists($referenceKey, $data)) {
                return null;
            }
            $referenceKeys[] = $referenceKey;
            $referenceValues[$targetIdentifier] = $data[$referenceKey];
        }
        $id = array_filter($referenceValues);
        if (empty($id)) {
            return null;
        }
        $associationValue = $this->entityManager->getRepository($associationMapping['targetEntity'])->find($id);

        return [
            'referenceKeys' => $referenceKeys,
            'associationKey' => $associationMapping['fieldName'],
            'associationValue' => $associationValue
        ];
    }

    function getClassName ($entity)
    {
        return $this->entityManager->getClassMetadata(get_class($entity))->rootEntityName;
    }
}
