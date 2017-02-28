<?php

namespace Alsciende\CerealBundle;

/**
 * Description of AssociationNormalizer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class AssociationNormalizer
{

    /** @var \Doctrine\ORM\Mapping\ClassMetadataFactory */
    private $factory;

    /** @var \Symfony\Component\Serializer\Serializer */
    private $serializer;

    function __construct (\Doctrine\ORM\EntityManager $em)
    {
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
    function normalize ($entity)
    {
        $metadata = $this->factory->getMetadataFor(get_class($entity));
        $data = $this->serializer->normalize($entity, null, ['groups' => ['json']]);

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
     * @param string $className
     * @return array
     */
    function findReferences ($data, $className)
    {
        $references = [];
        foreach($this->factory->getMetadataFor($className)->getAssociationMappings() as $mapping) {
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
     * @param \Doctrine\ORM\EntityManager $em
     * @return object
     * @throws \Alsciende\CerealBundle\Exception\InvalidForeignKeyException
     */
    function findReferencedEntity ($field, $reference, \Doctrine\ORM\EntityManager $em)
    {
        $qb = $em->createQueryBuilder();
        $qb->select($field)->from($reference['className'], $field);
        foreach($reference['joinColumns'] as $foreignKey => $condition) {
            $conditionString = sprintf("%s.%s = :%s", $field, $condition['referencedColumnName'], $foreignKey);
            $qb->andWhere($conditionString)->setParameter($foreignKey, $condition['referencedValue']);
        }

        try {
            return $qb->getQuery()->getSingleResult();
        } catch(\Doctrine\ORM\NoResultException $ex) {
            throw new \Alsciende\CerealBundle\Exception\InvalidForeignKeyException($reference);
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

}
