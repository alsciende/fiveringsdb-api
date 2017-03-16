<?php

namespace Alsciende\SerializerBundle\Service;

/**
 * References an object from another object
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ReferencingService implements ReferencingServiceInterface
{

    public function __construct (\Alsciende\SerializerBundle\Manager\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     *
     * @var \Alsciende\SerializerBundle\Manager\ObjectManagerInterface
     */
    private $objectManager;

    public function reference ($object)
    {
        $className = get_class($object);
        $targets = $this->objectManager->getAllTargetClasses($className);
        $data = [];
        foreach($targets as $associationKey => $targetClass) {
            $associationValue = $this->objectManager->readObject($object, $associationKey);
            list($referenceKey, $referenceValue) = $this->getReferenceFromAssociation($targetClass, $associationKey, $associationValue);
            $data[$referenceKey] = $referenceValue;
        }
        return $data;
    }

    /**
     * Return the reference corresponding to the assocation in the entity
     * 
     * @param string $targetClass
     * @param string $associationKey
     * @param object $associationValue
     * @return array
     */
    private function getReferenceFromAssociation ($targetClass, $associationKey, $associationValue)
    {
        $targetIdentifier = $this->objectManager->getSingleIdentifier($targetClass);
        $referenceValue = null;
        if($associationValue !== null) {
            $referenceValue = $this->objectManager->readObject($associationValue, $targetIdentifier);
        }
        $referenceKey = $associationKey . '_' . $targetIdentifier;
        return array($referenceKey, $referenceValue);
    }

    public function dereference($data, $type)
    {
        $object = new $type;
        $associations = $this->objectManager->findAssociations($type, $data);
        
        $updatedFields = [];
        foreach($associations as $association) {
            $updatedFields[$association['associationKey']] = $association['associationValue'];
        }
        
        $this->objectManager->updateObject($object, $updatedFields);

        return $object;
    }
}
