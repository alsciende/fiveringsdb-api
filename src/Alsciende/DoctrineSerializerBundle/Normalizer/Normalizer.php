<?php

namespace Alsciende\DoctrineSerializerBundle\Normalizer;

/**
 * Description of Normalizer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Normalizer
{

    /* @var \Alsciende\DoctrineSerializerBundle\Manager\ObjectManagerInterface */
    private $objectManager;

    /* @var \Symfony\Component\Serializer\Serializer */
    private $serializer;

    function __construct (\Alsciende\DoctrineSerializerBundle\Manager\ObjectManagerInterface $objectManager, \Symfony\Component\Serializer\Serializer $serializer)
    {
        $this->objectManager = $objectManager;
        $this->serializer = $serializer;
    }

    /**
     * Turn $entity into an array $data
     * Then find all the associations in $entity
     * And add them the corresponding references to $data
     * 
     * eg "article" => (object Article) becomes "article_id" => 2134
     * 
     */
    function normalize ($entity, $group = null)
    {
        $className = get_class($entity);
        
        // normalize scalar values
        $context = [];
        if(isset($group)) {
            $context['groups'] = array($group);
        }
        $data = $this->serializer->normalize($entity, null, $context);

        // normalize object values into references
        $targets = $this->objectManager->getAllTargetClasses($className);
        foreach($targets as $associationKey => $targetClass) {
            $associationValue = $this->objectManager->readObject($entity, $associationKey);
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

}
