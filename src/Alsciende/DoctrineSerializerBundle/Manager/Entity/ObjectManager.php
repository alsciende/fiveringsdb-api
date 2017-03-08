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
    function getAllTargetClasses ($className)
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
            $association = $this->findAssociation($data, $associationMapping);
            if(!$association) {
                throw new \InvalidArgumentException("Cannot find entity referenced by $identifierFieldName in data " . json_encode($data));
            }
            return $association['associationValue'];
        }
    }

    /**
     * {@inheritDoc}
     */
    function findAssociations ($className, $data)
    {
        $classMetadata = $this->entityManager->getClassMetadata($className);

        $associations = [];
        foreach($classMetadata->getAssociationMappings() as $mapping) {
            $association = $this->findAssociation($data, $mapping);
            if($association) {
                $associations[] = $association;
            }
        }

        return $associations;
    }

    /**
     * Returns a description of the association, including the foreign key value
     * as found in $data
     * 
     * @param type $data an array where the value of the foreign key can be found
     * @param type $associationMapping
     * @return array
     */
    private function findAssociation ($data, $associationMapping)
    {
        if(!$associationMapping['isOwningSide']) {
            return;
        }
        $referenceKeys = [];
        $id = [];
        foreach($associationMapping['sourceToTargetKeyColumns'] as $referenceKey => $targetIdentifier) {
            if(!key_exists($referenceKey, $data)) {
                return;
            }
            $referenceKeys[] = $referenceKey;
            $id[$targetIdentifier] = $data[$referenceKey];
        }
        $associationValue = $this->entityManager->getRepository($associationMapping['targetEntity'])->find($id);

        return [
            'referenceKeys' => $referenceKeys,
            'associationKey' => $associationMapping['fieldName'],
            'associationValue' => $associationValue
        ];
    }

}
