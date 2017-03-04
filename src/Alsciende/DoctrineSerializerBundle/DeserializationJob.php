<?php

namespace Alsciende\DoctrineSerializerBundle;

/**
 * Deserialization Job for exactly one entity
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeserializationJob
{

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /** @var AssociationNormalizer */
    private $normalizer;

    /** @var  \Symfony\Component\Validator\Validator\RecursiveValidator */
    private $validator;

    /* @var string */
    private $filepath;

    /* @var Annotation\Source */
    private $source;

    /* @var \Doctrine\ORM\Mapping\ClassMetadata */
    private $metadata;

    /* @var object */
    private $entity;

    /* @var array */
    private $original;

    /* @var array */
    private $incoming;

    /* @var array */
    private $changes;

    /* @var array */
    private $renamedKeys;

    function __construct ($filepath, $incoming, Annotation\Source $source)
    {
        $this->filepath = $filepath;
        $this->incoming = $incoming;
        $this->source = $source;
    }

    function run (\Doctrine\ORM\EntityManager $em, \Symfony\Component\Validator\Validator\RecursiveValidator $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->normalizer = new AssociationNormalizer($em);

        $this->metadata = $this->em->getClassMetadata($this->source->className);

        // find the entity based on the incoming identifier
        $this->entity = $this->findEntity();

        // normalize the entity in its original state
        $this->original = $this->normalizer->normalize($this->entity, $this->source->group);

        // compute changes between the normalized data
        $this->changes = array_diff($this->incoming, $this->original);

        // denormalize the associations in the incoming data
        $this->denormalizeIncomingAssociations();

        // update the entity with the field updated in incoming
        foreach ($this->changes as $field => $value) {
            if (isset($this->renamedKeys[$field])) {
                $field = $this->renamedKeys[$field];
                $value = $this->incoming[$field];
            }
            $this->metadata->setFieldValue($this->entity, $field, $value);
        }

        $errors = $this->validator->validate($this->entity);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            throw new \Exception($errorsString);
        }

        $this->em->merge($this->entity);
    }

    /**
     * Finds all the foreign keys in $incoming and replaces them with
     * a proper Doctrine association
     * 
     * eg "article_id" => 2134 becomes "article" => (object Article)
     */
    protected function denormalizeIncomingAssociations ()
    {
        $references = $this->normalizer->findReferences($this->incoming, $this->source->className);

        foreach ($references as $field => $reference) {
            $entity = $this->normalizer->findReferencedEntity($field, $reference, $this->em);
            if (!$entity) {
                throw new \InvalidArgumentException("Invalid reference " . json_encode($reference));
            }
            $this->incoming[$field] = $entity;
            foreach (array_keys($reference['joinColumns']) as $foreignKey) {
                unset($this->incoming[$foreignKey]);
                $this->renamedKeys[$foreignKey] = $field;
            }
        }
    }

    protected function findEntity ()
    {
        $identifierPairs = $this->getIdentifierPairs($this->metadata);

        $entity = $this->em->find($this->source->className, $identifierPairs);

        if (!isset($entity)) {
            $classname = $this->source->className;
            $entity = new $classname();
            foreach ($identifierPairs as $identifierField => $uniqueValue) {
                $this->metadata->setFieldValue($entity, $identifierField, $uniqueValue);
            }
        }

        return $entity;
    }

    /**
     * Returns the array of identifier keys/values that can be used with find()
     * to find the entity described by $incoming
     * 
     * If an identifier is a foreignIdentifier, find the foreign entity
     * 
     * @param \Doctrine\ORM\Mapping\ClassMetadata $metadata
     * @return array
     * @throws \InvalidArgumentException
     */
    function getIdentifierPairs (\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        // isIdentifierComposite
        // containsForeignIdentifier
        
        $pairs = [];

        $identifierFieldNames = $metadata->getIdentifierFieldNames();
        $fieldNames = $metadata->getFieldNames();
        foreach ($identifierFieldNames as $identifier) {
            if(in_array($identifier, $fieldNames)) {
                if (!isset($this->incoming[$identifier])) {
                    throw new \InvalidArgumentException('Missing identifier for entity ' . $metadata->getName() . ' in data ' . json_encode($this->incoming));
                }
                $pairs[$identifier] = $this->incoming[$identifier];
            } else {
                $associationMapping = $metadata->getAssociationMapping($identifier);
                $referenceMetadata = $this->normalizer->findReferenceMetadata($this->incoming, $associationMapping);
                $entity = $this->normalizer->findReferencedEntity($identifier, $referenceMetadata, $this->em);
                if(!$entity) {
                    throw new \InvalidArgumentException("Cannot find entity referenced by $identifier in data " . json_encode($this->incoming));
                }
                $pairs[$identifier] = $entity;
            }
        }

        return $pairs;
    }

    function getEntity ()
    {
        return $this->entity;
    }

    function getChanges ()
    {
        return $this->changes;
    }

    function getOriginal ()
    {
        return $this->original;
    }

    function getFilepath ()
    {
        return $this->filepath;
    }

}
