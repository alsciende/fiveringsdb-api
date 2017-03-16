<?php

namespace Alsciende\SerializerBundle\Serializer;

/**
 * Description of Serializer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Serializer
{

    public function __construct (
            \Alsciende\SerializerBundle\Service\StoringService $storingService,
            \Alsciende\SerializerBundle\Service\EncodingService $encoder,
            \Alsciende\SerializerBundle\Service\NormalizingServiceInterface $normalizingService,
            \Alsciende\SerializerBundle\Manager\ObjectManagerInterface $objectManager,
            \Alsciende\SerializerBundle\Manager\SourceManager $sourceManager,
            \Symfony\Component\Validator\Validator\RecursiveValidator $validator,
            \Doctrine\Common\Annotations\Reader $reader
            )
    {
        $this->storingService = $storingService;
        $this->encoder = $encoder;
        $this->normalizingService = $normalizingService;
        $this->objectManager = $objectManager;
        $this->sourceManager = $sourceManager;
        $this->validator = $validator;
        $this->reader = $reader;
    }

    /**
     * @var \Alsciende\SerializerBundle\Service\StoringService
     */
    private $storingService;

    /**
     * @var \Alsciende\SerializerBundle\Service\EncodingService
     */
    private $encoder;

    /**
     * @var \Alsciende\SerializerBundle\Service\NormalizingServiceInterface
     */
    private $normalizingService;

    /**
     * @var \Alsciende\SerializerBundle\Service\MergingService
     */
    private $mergingService;

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
            $reflectionClass = new \ReflectionClass($className);
            $annotation = $this->reader->getClassAnnotation($reflectionClass, 'Alsciende\SerializerBundle\Annotation\Source');
            if($annotation) {
                $source = new \Alsciende\SerializerBundle\Model\Source($className, $annotation->path, $annotation->break);
                /* @var $reflectionProperty \ReflectionProperty */
                foreach ($reflectionClass->getProperties() as $reflectionProperty) {
                    $annotation = $this->reader->getPropertyAnnotation($reflectionProperty, 'Alsciende\SerializerBundle\Annotation\Source');
                    if($annotation) {
                        $source->addProperty($reflectionProperty->name, $annotation->type);
                    }
                }
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
        $data = $fragment->getData();
        $className = $fragment->getBlock()->getSource()->getClassName();

        // find the entity based on the incoming identifier
        $entity = $this->findOrCreateObject($className, $data);
        
        $result = $this->normalizingService->denormalize($data, $className, $fragment->getBlock()->getSource()->getProperties());
        
        $this->objectManager->updateObject($entity, $result);
        
        $errors = $this->validator->validate($entity);
        if(count($errors) > 0) {
            $errorsString = (string) $errors;
            throw new \Exception($errorsString);
        }

//        $fragment->setEntity($entity);
//        $fragment->setOriginal();
//        $fragment->setChanges($changes);
//        $fragment->setData($result);
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
