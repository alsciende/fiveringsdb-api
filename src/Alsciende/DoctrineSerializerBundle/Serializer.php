<?php

namespace Alsciende\DoctrineSerializerBundle;

/**
 * Description of Serializer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Serializer
{

    /* @var Manager\SourceManager */
    private $sourceManager;

    /* @var \Symfony\Component\Validator\Validator\RecursiveValidator */
    private $validator;

    /* @var Normalizer */
    private $normalizer;

    /* @var \Doctrine\Common\Annotations\Reader */
    private $reader;

    /* @var Manager\ObjectManagerInterface */
    private $objectManager;

    public function __construct (Manager\ObjectManagerInterface $objectManager, Manager\SourceManager $sourceManager, \Symfony\Component\Validator\Validator\RecursiveValidator $validator, \Doctrine\Common\Annotations\Reader $reader, \Alsciende\DoctrineSerializerBundle\Normalizer $normalizer)
    {
        $this->objectManager = $objectManager;
        $this->sourceManager = $sourceManager;
        $this->validator = $validator;
        $this->reader = $reader;
        $this->normalizer = $normalizer;
    }

    /**
     * 
     * @return Model\Fragment[]
     */
    public function import ()
    {

        /* @var $encoder JsonFileEncoder */
        $encoder = new JsonFileEncoder();

        $classNames = $this->objectManager->getAllManagedClassNames();
        foreach($classNames as $className) {
            /* @var $annotation \Alsciende\DoctrineSerializerBundle\Annotation\Source */
            $annotation = $this->reader->getClassAnnotation(new \ReflectionClass($className), 'Alsciende\DoctrineSerializerBundle\Annotation\Source');
            if($annotation) {
                $source = new Model\Source($className, $annotation->path, $annotation->break, $annotation->group);
                $this->sourceManager->addSource($source);
            }
        }
        $sources = $this->sourceManager->getSources();

        $result = [];

        foreach($sources as $source) {

            $fragments = $encoder->decode($source);

            foreach($fragments as $fragment) {
                $this->importFragment($fragment);
            }

            $this->objectManager->flush();

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
        $incoming = $fragment->getIncoming();
        $className = $fragment->getSource()->getClassName();
        $group = $fragment->getSource()->getGroup();

        // find the entity based on the incoming identifier
        $entity = $this->findOrCreateObject($className, $incoming);

        // normalize the entity in its original state
        $original = $this->normalizer->normalize($entity, $group);

        // compute changes between the normalized data
        $changes = array_diff($incoming, $original);

        // denormalize the associations in the incoming data
        $associations = $this->objectManager->findAssociations($className, $incoming);

        $renamedKeys = [];
        // replace the references with associations
        foreach($associations as $association) {
            foreach($association['referenceKeys'] as $referenceKey) {
                unset($incoming[$referenceKey]);
                $renamedKeys[$referenceKey] = $association['associationKey'];
            }
            $incoming[$association['associationKey']] = $association['associationValue'];
        }

        // update the entity with the field updated in incoming
        $updatedFields = [];
        foreach($changes as $field => $value) {
            if(isset($renamedKeys[$field])) {
                $field = $renamedKeys[$field];
                $value = $incoming[$field];
            }
            $updatedFields[$field] = $value;
        }
        $this->objectManager->updateObject($entity, $updatedFields);
        
        $errors = $this->validator->validate($entity);
        if(count($errors) > 0) {
            $errorsString = (string) $errors;
            throw new \Exception($errorsString);
        }
        
        $fragment->setEntity($entity);
        $fragment->setOriginal($original);
        $fragment->setChanges($changes);
        $fragment->setIncoming($incoming);
    }

    /**
     * 
     */
    function findOrCreateObject ($className, $data)
    {
        $identifiers = $this->objectManager->getIdentifierValues($className, $data);

        $entity = $this->objectManager->findObject($className, $identifiers);

        if(!isset($entity)) {
            $entity = new $className();
            $this->objectManager->updateObject($entity, $identifiers);
        }

        return $entity;
    }

}
