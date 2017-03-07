<?php

namespace Alsciende\DoctrineSerializerBundle;

/**
 * Description of Serializer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Serializer
{

    /* @var \Doctrine\ORM\EntityManager */
    private $entityManager;

    /* @var Manager\SourceManager */
    private $sourceManager;

    /* @var \Symfony\Component\Validator\Validator\RecursiveValidator */
    private $validator;

    /* @var AssociationNormalizer */
    private $normalizer;

    /* @var \Doctrine\Common\Annotations\Reader */
    private $reader;

    public function __construct (\Doctrine\ORM\EntityManager $entityManager, Manager\SourceManager $sourceManager, \Symfony\Component\Validator\Validator\RecursiveValidator $validator, \Doctrine\Common\Annotations\Reader $reader)
    {
        $this->entityManager = $entityManager;
        $this->sourceManager = $sourceManager;
        $this->validator = $validator;
        $this->reader = $reader;
        $this->normalizer = new AssociationNormalizer($entityManager);
    }

    /**
     * 
     * @return Model\Fragment[]
     */
    public function import ()
    {

        /* @var $encoder JsonFileEncoder */
        $encoder = new JsonFileEncoder();

        $allMetadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        foreach($allMetadata as $metadata) {
            $className = $metadata->getName();
            /* @var $annotation \Alsciende\DoctrineSerializerBundle\Annotation\Source */
            $annotation = $this->reader->getClassAnnotation(new \ReflectionClass($className), 'Alsciende\DoctrineSerializerBundle\Annotation\Source');
            if ($annotation) {
                $source = new Model\Source($className, $annotation->path, $annotation->break, $annotation->group);
                $this->sourceManager->addSource($source);
            }
        }
        $sources = $this->sourceManager->getSources();

        $result = [];

        foreach ($sources as $source) {

            $fragments = $encoder->decode($source);

            foreach ($fragments as $fragment) {
                $this->importFragment($fragment);
            }

            $this->entityManager->flush();

            $result = array_merge($result, $fragments);
        }

        return $result;
    }

    /**
     * 
     * @param \Alsciende\DoctrineSerializerBundle\Model\Fragment $fragment
     * @throws \Exception
     */
    public function importFragment (Model\Fragment $fragment)
    {
        $classMetadata = $this->entityManager->getClassMetadata($fragment->getSource()->getClassName());

        // find the entity based on the incoming identifier
        $fragment->setEntity($this->findEntity($fragment));

        // normalize the entity in its original state
        $fragment->setOriginal($this->normalizer->normalize($fragment->getEntity(), $fragment->getSource()->getGroup()));

        // compute changes between the normalized data
        $fragment->setChanges(array_diff($fragment->getIncoming(), $fragment->getOriginal()));

        // denormalize the associations in the incoming data
        $references = $this->normalizer->findReferences($fragment->getIncoming(), $classMetadata);
        $findForeignKeyValues = $this->normalizer->findForeignKeyValues($references);

        $renamedKeys = [];
        $incoming = $fragment->getIncoming();
        
        // replace the references with foreignKeys
        foreach($findForeignKeyValues as $findForeignKeyValue) {
            foreach($findForeignKeyValue['joinColumns'] as $joinColumn) {
                unset($incoming[$joinColumn]);
                $renamedKeys[$joinColumn] = $findForeignKeyValue['foreignKey'];
            }
            $incoming[$findForeignKeyValue['foreignKey']] = $findForeignKeyValue['foreignValue'];
        }
        
        // update the entity with the field updated in incoming
        foreach ($fragment->getChanges() as $field => $value) {
            if (isset($renamedKeys[$field])) {
                $field = $renamedKeys[$field];
                $value = $incoming[$field];
            }
            $classMetadata->setFieldValue($fragment->getEntity(), $field, $value);
        }
        
        $fragment->setIncoming($incoming);

        $errors = $this->validator->validate($fragment->getEntity());
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            throw new \Exception($errorsString);
        }

        $this->entityManager->merge($fragment->getEntity());
    }

    /**
     * Find the entity referenced by the identifiers in $fragment->incoming
     * 
     * @param \Alsciende\DoctrineSerializerBundle\Model\Fragment $fragment
     * @return \Alsciende\DoctrineSerializerBundle\classname
     */
    protected function findEntity (Model\Fragment $fragment)
    {
        $classMetadata = $this->entityManager->getClassMetadata($fragment->getSource()->getClassName());

        $identifierPairs = $this->getIdentifierPairs($fragment);

        $entity = $this->entityManager->find($fragment->getSource()->getClassName(), $identifierPairs);

        if (!isset($entity)) {
            $classname = $fragment->getSource()->getClassName();
            $entity = new $classname();
            foreach ($identifierPairs as $identifierField => $uniqueValue) {
                $classMetadata->setFieldValue($entity, $identifierField, $uniqueValue);
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
     * @return array
     * @throws \InvalidArgumentException
     */
    function getIdentifierPairs (Model\Fragment $fragment)
    {
        $classMetadata = $this->entityManager->getClassMetadata($fragment->getSource()->getClassName());

        $pairs = [];

        $identifierFieldNames = $classMetadata->getIdentifierFieldNames();
        $fieldNames = $classMetadata->getFieldNames();
        foreach ($identifierFieldNames as $identifierFieldName) {
            $pairs[$identifierFieldName] = $this->getIdentifierValue($fragment, $identifierFieldName, $fieldNames);
        }

        return $pairs;
    }

    function getIdentifierValue (Model\Fragment $fragment, $identifierFieldName, $fieldNames)
    {
        $classMetadata = $this->entityManager->getClassMetadata($fragment->getSource()->getClassName());

        if (in_array($identifierFieldName, $fieldNames)) {
            if (!isset($fragment->getIncoming()[$identifierFieldName])) {
                throw new \InvalidArgumentException("Missing identifier for entity " . $fragment->getSource()->getClassName() . " in data " . json_encode($fragment->getIncoming()));
            }
            return $fragment->getIncoming()[$identifierFieldName];
        } else {
            $associationMapping = $classMetadata->getAssociationMapping($identifierFieldName);
            $referenceMetadata = $this->normalizer->findReferenceMetadata($fragment->getIncoming(), $associationMapping);
            $entity = $this->normalizer->findReferencedEntity($identifierFieldName, $referenceMetadata);
            if (!$entity) {
                throw new \InvalidArgumentException("Cannot find entity referenced by $identifierFieldName in data " . json_encode($fragment->getIncoming()));
            }
            return $entity;
        }
    }

}
