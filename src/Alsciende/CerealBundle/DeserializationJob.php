<?php

namespace Alsciende\CerealBundle;

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
    private $changes;

    /* @var array */
    private $renamedKeys;

    function __construct ($filepath, $incoming, $classname)
    {
        $this->filepath = $filepath;
        $this->incoming = $incoming;
        $this->classname = $classname;
    }

    function run (\Doctrine\ORM\EntityManager $em, \Symfony\Component\Validator\Validator\RecursiveValidator $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->normalizer = new AssociationNormalizer($em);

        $this->metadata = $this->em->getClassMetadata($this->classname);

        // find the entity based on the incoming identifier
        $this->entity = $this->findEntity();

        // normalize the entity in its original state
        $this->original = $this->normalizer->normalize($this->entity);

        // compute changes between the normalized data
        $this->changes = array_diff($this->incoming, $this->original);

        // denormalize the associations in the incoming data
        $this->denormalizeIncomingAssociations();

        // update the entity with the field updated in incoming
        foreach($this->changes as $field => $value) {
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
     * Finds all the foreign keys in $incoming and replaces them with
     * a proper Doctrine association
     * 
     * eg "article_id" => 2134 becomes "article" => (object Article)
     * 
     * @throws \Alsciende\CerealBundle\Exception\InvalidForeignKeyException
     */
    protected function denormalizeIncomingAssociations ()
    {
        $references = $this->normalizer->findReferences($this->incoming, $this->classname);

        foreach($references as $field => $reference) {
            $entity = $this->normalizer->findReferencedEntity($field, $reference, $this->em);
            if(!$entity) {
                throw new \InvalidArgumentException("Invalid reference ".json_encode($reference));
            }
            $this->incoming[$field] = $entity;
            foreach(array_keys($reference['joinColumns']) as $foreignKey) {
                unset($this->incoming[$foreignKey]);
                $this->renamedKeys[$foreignKey] = $field;
            }
        }
    }

    protected function findEntity ()
    {
        list($identifierField, $uniqueValue) = $this->getIdentifierPair($this->metadata);

        if($uniqueValue) {
            $entity = $this->em->find($this->classname, [$identifierField => $uniqueValue]);
        }
        
        if(!isset($entity)) {
            $classname = $this->classname;
            $entity = new $classname();
            $this->metadata->setFieldValue($entity, $identifierField, $uniqueValue);
        }

        return $entity;
    }
    
    
    function getIdentifierPair (\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $identifier = $this->normalizer->getSingleIdentifier($metadata);

        if(!isset($this->incoming[$identifier])) {
            if(empty($metadata->idGenerator)) {
                throw new \InvalidArgumentException('Missing identifier for entity '.$metadata->getName().' in data '.json_encode($this->incoming));
            } else {
                return array($identifier, null);
            }
        }

        return array($identifier, $this->incoming[$identifier]);
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
