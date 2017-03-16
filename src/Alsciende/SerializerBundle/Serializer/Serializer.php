<?php

namespace Alsciende\SerializerBundle\Serializer;

/**
 * Description of Serializer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Serializer
{

    /**
     * @var \Alsciende\SerializerBundle\Service\StoringService
     */
    private $storingService;

    /**
     * @var \Alsciende\SerializerBundle\Encoder\Encoder
     */
    private $encoder;

    /**
     * @var \Alsciende\SerializerBundle\Normalizer\Normalizer
     */
    private $normalizer;

    /**
     * @var \Alsciende\SerializerBundle\Manager\SourceManager
     */
    private $sourceManager;

    /**
     * @var \Symfony\Component\Validator\Validator\RecursiveValidator
     */
    private $validator;

    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $reader;

    /**
     * @var \Alsciende\SerializerBundle\Manager\ObjectManagerInterface
     */
    private $objectManager;

    public function __construct (\Alsciende\SerializerBundle\Service\StoringService $storingService, \Alsciende\SerializerBundle\Encoder\Encoder $encoder, \Alsciende\SerializerBundle\Normalizer\Normalizer $normalizer, \Alsciende\SerializerBundle\Manager\ObjectManagerInterface $objectManager, \Alsciende\SerializerBundle\Manager\SourceManager $sourceManager, \Symfony\Component\Validator\Validator\RecursiveValidator $validator, \Doctrine\Common\Annotations\Reader $reader)
    {
        $this->storingService = $storingService;
        $this->encoder = $encoder;
        $this->normalizer = $normalizer;
        $this->objectManager = $objectManager;
        $this->sourceManager = $sourceManager;
        $this->validator = $validator;
        $this->reader = $reader;
    }

    /**
     * 
     * @return Model\Fragment[]
     */
    public function import ()
    {
        $result = [];
        foreach($this->getSources() as $source) {
            $result = array_merge($result, $this->importSource($source));
        }
        return $result;
    }

    
    
    public function importSource (\Alsciende\SerializerBundle\Model\Source $source)
    {
        $result = [];
        foreach($this->storingService->retrieve($source) as $block) {
            $result = array_merge($result, $this->importBlock($block));
        }
        $this->objectManager->flush();
        return $result;
    }

    public function importBlock (\Alsciende\SerializerBundle\Model\Block $block)
    {
        $result = [];
        foreach($this->encoder->decode($block) as $fragment) {
            $this->importFragment($fragment);
            $result[] = $fragment;
        }
        return $result;
    }

    public function getSources ()
    {
        $classNames = $this->objectManager->getAllManagedClassNames();
        foreach($classNames as $className) {
            /* @var $annotation \Alsciende\SerializerBundle\Annotation\Source */
            $annotation = $this->reader->getClassAnnotation(new \ReflectionClass($className), 'Alsciende\SerializerBundle\Annotation\Source');
            if($annotation) {
                $source = new \Alsciende\SerializerBundle\Model\Source($className, $annotation->path, $annotation->break, $annotation->group);
                $this->sourceManager->addSource($source);
            }
        }
        return $this->sourceManager->getSources();
    }

    /**
     * 
     * @param \Alsciende\SerializerBundle\Model\Fragment $fragment
     * @throws \Exception
     */
    public function importFragment (\Alsciende\SerializerBundle\Model\Fragment $fragment)
    {
        $incoming = $fragment->getData();
        $className = $fragment->getBlock()->getSource()->getClassName();
        $group = $fragment->getBlock()->getSource()->getGroup();

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
        $fragment->setData($incoming);
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
