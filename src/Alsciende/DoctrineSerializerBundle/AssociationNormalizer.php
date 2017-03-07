<?php

namespace Alsciende\DoctrineSerializerBundle;

/**
 * Description of AssociationNormalizer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class AssociationNormalizer
{

    /* @var Manager\ObjectManagerInterface */
    private $objectManager;

    /* @var \Symfony\Component\Serializer\Serializer */
    private $serializer;

    function __construct (Manager\ObjectManagerInterface $objectManager, \Symfony\Component\Serializer\Serializer $serializer)
    {
        $this->objectManager = $objectManager;
        $this->serializer = $serializer;
    }

    /**
     * Turn $entity into an array $data, then
     * find all the associations in $entity 
     * and add them to $data as a foreign key
     * 
     * eg "article" => (object Article) becomes "article_id" => 2134
     * 
     */
    function normalize ($entity, $group = null)
    {
        $className = get_class($entity);
        $context = [];
        if(isset($group)) {
            $context['groups'] = array($group);
        }
        $data = $this->serializer->normalize($entity, null, $context);

        $dependencies = $this->objectManager->getDependingClassNames($className);
        foreach($dependencies as $foreignKey => $foreignClassName) {
            list($compositeField, $value) = $this->normalizeOwningSideAssociation($entity, $foreignKey, $foreignClassName);
            $data[$compositeField] = $value;
        }

        return $data;
    }

    /**
     * 
     * @param object $entity
     * @param string $foreignKey
     * @param string $foreignClassName
     * @return array
     */
    function normalizeOwningSideAssociation ($entity, $foreignKey, $foreignClassName)
    {
        $identifier = $this->objectManager->getSingleIdentifier($foreignClassName);
        $target = $this->objectManager->readObject($entity, $foreignKey);
        if($target === null) {
            $value = null;
        } else {
            $value = $this->objectManager->readObject($target, $identifier);
        }
        $compositeField = $foreignKey . '_' . $identifier;
        return array($compositeField, $value);
    }

}
