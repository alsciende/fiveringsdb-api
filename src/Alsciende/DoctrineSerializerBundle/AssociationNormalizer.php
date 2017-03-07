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
    
    /* @var \Doctrine\ORM\EntityManager */
    private $em;
    
    /* @var \Doctrine\ORM\Mapping\ClassMetadataFactory */
    private $factory;

    /* @var \Symfony\Component\Serializer\Serializer */
    private $serializer;

    function __construct (Manager\ReferenceManagerInterface $referenceManager, \Doctrine\ORM\EntityManager $em)
    {
        $this->referenceManager = $referenceManager;
        
        $this->em = $em;
        $this->factory = new \Doctrine\ORM\Mapping\ClassMetadataFactory();
        $this->factory->setEntityManager($em);

        $classMetadataFactory = new \Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory(new \Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader(new \Doctrine\Common\Annotations\AnnotationReader()));
        $normalizer = new \Symfony\Component\Serializer\Normalizer\ObjectNormalizer($classMetadataFactory);
        $this->serializer = new \Symfony\Component\Serializer\Serializer(array($normalizer));
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
        $metadata = $this->factory->getMetadataFor(get_class($entity));
        $context = [];
        if(isset($group)) {
            $context['groups'] = array($group);
        }
        $data = $this->serializer->normalize($entity, null, $context);

        foreach($metadata->getAssociationMappings() as $mapping) {
            if($mapping['isOwningSide']) {
                list($compositeField, $value) = $this->normalizeOwningSideAssociation($entity, $metadata, $mapping);
            }
            $data[$compositeField] = $value;
        }

        return $data;
    }

    function normalizeOwningSideAssociation ($entity, $metadata, $mapping)
    {
        $targetMetadata = $this->factory->getMetadataFor($mapping['targetEntity']);
        $identifier = $this->getSingleIdentifier($targetMetadata);
        $target = $metadata->getFieldValue($entity, $mapping['fieldName']);
        if($target === null) {
            $value = null;
        } else {
            $value = $targetMetadata->getFieldValue($target, $identifier);
        }
        $compositeField = $mapping['fieldName'] . '_' . $identifier;
        return array($compositeField, $value);
    }

    function getSingleIdentifier (\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $identifierFieldNames = $metadata->getIdentifierFieldNames();
        if(count($identifierFieldNames) > 1) {
            throw new InvalidArgumentException('Too many identifiers for ' . $metadata->getName());
        }
        return $identifierFieldNames[0];
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
