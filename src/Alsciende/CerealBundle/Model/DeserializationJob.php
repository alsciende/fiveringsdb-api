<?php

namespace Alsciende\CerealBundle\Model;

/**
 * Deserialization Job for exactly one entity
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeserializationJob
{

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /** @var \Symfony\Component\Serializer\Serializer */
    private $serializer;

    /** @var  \Symfony\Component\Validator\Validator\RecursiveValidator */
    private $validator;

    /* @var string */
    private $filepath;

    /* @var string */
    private $classname;

    /* @var \Doctrine\ORM\Mapping\ClassMetadata */
    private $metadata;

    /* @var object */
    private $entity;

    /* @var array */
    private $original;

    /* @var array */
    private $incoming;

    /* @var array */
    private $differences;

    /* @var array */
    private $renamedKeys;

    function __construct (\Doctrine\ORM\EntityManager $em, \Symfony\Component\Validator\Validator\RecursiveValidator $validator, DecodedJsonFile $decodedJsonFile, $classname)
    {
        $this->em = $em;

        $classMetadataFactory = new \Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory(new \Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader(new \Doctrine\Common\Annotations\AnnotationReader()));
        $normalizer = new \Symfony\Component\Serializer\Normalizer\ObjectNormalizer($classMetadataFactory);
        $this->serializer = new \Symfony\Component\Serializer\Serializer(array($normalizer));

        $this->validator = $validator;

        $this->filepath = $decodedJsonFile->getFilepath();
        $this->incoming = $decodedJsonFile->getData();
        $this->classname = $classname;
        $this->metadata = $this->em->getClassMetadata($this->classname);

        // find the entity based on the incoming identifier
        $this->entity = $this->findEntity();

        // normalize the entity in its original state
        $this->original = $this->serializer->normalize($this->entity, null, ['groups' => ['json']]);
        $this->normalizeOriginalAssociations();

        // compute differences between the normalized data
        $this->differences = array_diff($this->incoming, $this->original);

        // denormalize the associations in the incoming data
        $this->denormalizeIncomingAssociations();

        // update the entity with the field updated in incoming
        foreach($this->differences as $field => $value) {
            if(isset($this->renamedKeys[$field])) {
                $field = $this->renamedKeys[$field];
                $value = $this->incoming[$field];
            }
            $this->metadata->setFieldValue($this->entity, $field, $value);
        }

        $errors = $this->validator->validate($this->entity);
        if(count($errors) > 0) {
            $errorsString = (string) $errors;
            throw new \Exception($errorsString);
        }

        $this->em->merge($this->entity);
    }

    /**
     * Finds all the associations in $metadata and add them to $original as a 
     * foreign key
     * 
     * eg "article" => (object Article) becomes "article_id" => 2134
     * 
     */
    protected function normalizeOriginalAssociations ()
    {
        foreach($this->metadata->getAssociationMappings() as $mapping) {
            $targetMetadata = $this->em->getClassMetadata($mapping['targetEntity']);
            $identifier = $this->getSingleIdentifier($targetMetadata);
            $target = $this->metadata->getFieldValue($this->entity, $mapping['fieldName']);
            if($target === null) {
                $value = null;
            } else {
                $value = $targetMetadata->getFieldValue($target, $identifier);
            }
            $compositeField = $mapping['fieldName'] . '_' . $identifier;
            $this->original[$compositeField] = $value;
        }
    }

    /**
     * Finds all the foreign keys in $incoming and replaces them with
     * a proper Doctrine association
     * 
     * eg "article_id" => 2134 becomes "article" => (object Article)
     * 
     * @throws \Alsciende\CerealBundle\Exception\InvalidForeignKeyException
     */
    protected function denormalizeIncomingAssociations ()
    {
        $associations = [];

        foreach($this->metadata->getAssociationMappings() as $mapping) {
            $qb = $this->em->createQueryBuilder();
            $qb->select($mapping['fieldName'])->from($mapping['targetEntity'], $mapping['fieldName']);

            $keys = [];
            foreach($mapping['joinColumns'] as $index => $joinColumn) {
                if(key_exists($joinColumn['name'], $this->incoming)) {
                    $keys[] = $key = $joinColumn['name'];
                    $value = $this->incoming[$key];
                    $condition = sprintf("%s.%s = ?%d", $mapping['fieldName'], $joinColumn['referencedColumnName'], $index);
                    $qb->andWhere($condition)->setParameter($index, $value);
                } else {
                    continue 2; // next $mapping
                }
            }

            try {
                $result = $qb->getQuery()->getSingleResult();
            } catch(\Doctrine\ORM\NoResultException $ex) {
                throw new \Alsciende\CerealBundle\Exception\InvalidForeignKeyException($this->incoming, $keys, $this->classname);
            }

            $associations[$mapping['fieldName']] = $result;
            foreach($keys as $key) {
                $this->renamedKeys[$key] = $mapping['fieldName'];
                unset($this->incoming[$key]);
            }
        }

        $this->incoming = array_merge($this->incoming, $associations);
    }

    protected function findEntity ()
    {
        list($identifierField, $uniqueValue) = $this->getIdentifierPair($this->metadata);

        $entity = $this->em->find($this->classname, [$identifierField => $uniqueValue]);
        if(!$entity) {
            $classname = $this->classname;
            $entity = new $classname();
            $this->metadata->setFieldValue($entity, $identifierField, $uniqueValue);
        }

        return $entity;
    }

    protected function getSingleIdentifier (\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $identifierFieldNames = $metadata->getIdentifierFieldNames();
        if(count($identifierFieldNames) > 1) {
            throw new InvalidArgumentException('Too many identifiers for ' . $metadata->getName());
        }
        return $identifierFieldNames[0];
    }

    protected function getIdentifierPair (\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $identifier = $this->getSingleIdentifier($metadata);

        if(!isset($this->incoming[$identifier])) {
            throw new InvalidArgumentException('Missing identifier');
        }

        return array($identifier, $this->incoming[$identifier]);
    }

    function getEntity ()
    {
        return $this->entity;
    }

    function getDifferences ()
    {
        return $this->differences;
    }

    function getOriginal ()
    {
        return $this->original;
    }

}
