<?php

namespace Alsciende\DoctrineSerializerBundle;

/**
 * Description of AssociationNormalizer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class AssociationNormalizer
{
    /* @var \Doctrine\ORM\EntityManager */
    private $em;
    
    /* @var \Doctrine\ORM\Mapping\ClassMetadataFactory */
    private $factory;

    /* @var \Symfony\Component\Serializer\Serializer */
    private $serializer;

    function __construct (\Doctrine\ORM\EntityManager $em)
    {
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

    /**
     * Return an array listing the associations in $metadata that exist in $data,
     * with the relevant information to find the referenced entity
     * 
     * @param array $data
     * @param \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
     * @return array
     */
    function findReferences ($data, \Doctrine\ORM\Mapping\ClassMetadata $classMetadata)
    {
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
    function findReferenceMetadata ($data, $associationMapping)
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
    function findReferencedEntity ($field, $reference)
    {
        $qb = $this->em->createQueryBuilder();
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
            $entity = $this->findReferencedEntity($field, $reference);
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
