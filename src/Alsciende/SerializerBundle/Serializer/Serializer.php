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
            \Alsciende\SerializerBundle\Service\EncodingService $encodingService,
            \Alsciende\SerializerBundle\Service\NormalizingServiceInterface $normalizingService,
            \Alsciende\SerializerBundle\Manager\ObjectManagerInterface $objectManager
            )
    {
        $this->storingService = $storingService;
        $this->encodingService = $encodingService;
        $this->normalizingService = $normalizingService;
        $this->objectManager = $objectManager;
    }

    /**
     * @var \Alsciende\SerializerBundle\Service\StoringService
     */
    private $storingService;

    /**
     * @var \Alsciende\SerializerBundle\Service\EncodingService
     */
    private $encodingService;

    /**
     * @var \Alsciende\SerializerBundle\Service\NormalizingServiceInterface
     */
    private $normalizingService;

    /**
     * @var \Alsciende\SerializerBundle\Manager\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * 
     * @param \Alsciende\SerializerBundle\Model\Source $source
     * @return array
     */
    public function importSource (\Alsciende\SerializerBundle\Model\Source $source)
    {
        $result = [];
        foreach($this->storingService->retrieve($source) as $block) {
            $result = array_merge($result, $this->importBlock($block));
        }
        return $result;
    }

    /**
     * 
     * @param \Alsciende\SerializerBundle\Model\Block $block
     * @return array
     */
    public function importBlock (\Alsciende\SerializerBundle\Model\Block $block)
    {
        $result = [];
        foreach($this->encodingService->decode($block) as $fragment) {
            $result[] = $this->importFragment($fragment);
        }
        return $result;
    }

    /**
     * 
     * @param \Alsciende\SerializerBundle\Model\Fragment $fragment
     * @throws \Exception
     * @return array
     */
    public function importFragment (\Alsciende\SerializerBundle\Model\Fragment $fragment)
    {
        $data = $fragment->getData();
        $className = $fragment->getBlock()->getSource()->getClassName();
        $properties = $fragment->getBlock()->getSource()->getProperties();
        
        $result = [ 'data' => $data ];
        
        // find the entity based on the incoming identifier
        $entity = $this->objectManager->findOrCreateObject($data, $className);
        
        // denormalize the designated properties of the data into an array
        $array = $this->normalizingService->denormalize($data, $className, $properties);
        $result['array'] = $array;
        $result['original'] = $this->getOriginal($entity, $array);
        
        // update the entity with the values of the denormalized array
        $this->objectManager->updateObject($entity, $array);
        $result['entity'] = $entity;
        
        return $result;
    }
    
    public function getOriginal($entity, $array)
    {
        $result = [];
        
        foreach(array_keys($array) as $property) {
            $result[$property] = $this->objectManager->readObject($entity, $property);
        }
        
        return $result;
    }

}
