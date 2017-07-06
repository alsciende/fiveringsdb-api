<?php

namespace Alsciende\SerializerBundle\Manager;

/**
 * Description of BaseObjectManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
abstract class BaseObjectManager implements ObjectManagerInterface
{
    function findOrCreateObject ($data, $className)
    {
        $identifiers = $this->getIdentifierValues($data, $className);

        $entity = $this->findObject($identifiers, $className);

        if (isset($entity)) {
            return $entity;
        }

        $entity = new $className();
        $this->updateObject($entity, $identifiers);
        return $entity;
    }

    /**
     * Return the reference corresponding to the assocation in the entity
     *
     * @param string $targetClass
     * @param string $associationKey
     * @param object $associationValue
     * @return array
     */
    function getReferenceFromAssociation ($targetClass, $associationKey, $associationValue)
    {
        $targetIdentifier = $this->getSingleIdentifier($targetClass);
        $referenceValue = null;
        if ($associationValue !== null) {
            $referenceValue = $this->readObject($associationValue, $targetIdentifier);
        }
        $referenceKey = $associationKey . '_' . $targetIdentifier;
        return array($referenceKey, $referenceValue);
    }

}
