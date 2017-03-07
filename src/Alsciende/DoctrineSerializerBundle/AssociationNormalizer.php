<?php

namespace Alsciende\DoctrineSerializerBundle;

/**
 * Description of AssociationNormalizer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class AssociationNormalizer
{
    
    /* @var Manager\ReferenceManagerInterface */
    private $referenceManager;
    
    /* @var \Symfony\Component\Serializer\Serializer */
    private $serializer;

    function __construct (Manager\ReferenceManagerInterface $referenceManager, \Symfony\Component\Serializer\Serializer $serializer)
    {
        $this->referenceManager = $referenceManager;
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

        $dependencies = $this->referenceManager->getDependingClassNames($className);
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
        $identifier = $this->referenceManager->getSingleIdentifier($foreignClassName);
        $target = $this->referenceManager->readEntity($entity, $foreignKey);
        if($target === null) {
            $value = null;
        } else {
            $value = $this->referenceManager->readEntity($target, $identifier);
        }
        $compositeField = $foreignKey . '_' . $identifier;
        return array($compositeField, $value);
    }

    /**
     * Finds all the foreign keys in $data and the entity associated
     * 
     * eg ["article_id" => 2134] returns 
     * array([ "foreignKey" => "article", "foreignValue" => (object Article), "joinColumns" => [ "article_id"] ])
     * 
     * @return array
     */
    public function findForeignKeyValues ($references)
    {
        $result = [];

        foreach ($references as $field => $reference) {
            $entity = $this->referenceManager->findReferencedEntity($field, $reference);
            if (!$entity) {
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

}
