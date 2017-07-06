<?php

namespace Alsciende\SerializerBundle\Serializer;

use Alsciende\SerializerBundle\Manager\ObjectManagerInterface;
use Alsciende\SerializerBundle\Model\Block;
use Alsciende\SerializerBundle\Model\Fragment;
use Alsciende\SerializerBundle\Model\Source;
use Alsciende\SerializerBundle\Service\EncodingService;
use Alsciende\SerializerBundle\Service\NormalizingServiceInterface;
use Alsciende\SerializerBundle\Service\StoringService;
use Exception;

/**
 * Description of Serializer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Serializer
{

    public function __construct (StoringService $storingService, EncodingService $encodingService, NormalizingServiceInterface $normalizingService, ObjectManagerInterface $objectManager)
    {
        $this->storingService = $storingService;
        $this->encodingService = $encodingService;
        $this->normalizingService = $normalizingService;
        $this->objectManager = $objectManager;
    }

    /**
     * @var StoringService
     */
    private $storingService;

    /**
     * @var EncodingService
     */
    private $encodingService;

    /**
     * @var NormalizingServiceInterface
     */
    private $normalizingService;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     *
     * @param Source $source
     * @return array
     */
    public function importSource (Source $source)
    {
        $result = [];

        $blocks = $this->storingService->retrieve($source);
        if ($blocks) {
            foreach ($blocks as $block) {
                $result = array_merge($result, $this->importBlock($block));
            }
        }

        return $result;
    }

    /**
     *
     * @param Block $block
     * @return array
     */
    public function importBlock (Block $block)
    {
        $result = [];
        foreach ($this->encodingService->decode($block) as $fragment) {
            $result[] = $this->importFragment($fragment);
        }
        return $result;
    }

    /**
     *
     * @param Fragment $fragment
     * @throws Exception
     * @return array
     */
    public function importFragment (Fragment $fragment)
    {
        $data = $fragment->getData();
        $className = $fragment->getBlock()->getSource()->getClassName();
        $properties = $fragment->getBlock()->getSource()->getProperties();

        $result = ['data' => $data];

        // find the entity based on the incoming identifier
        $entity = $this->objectManager->findOrCreateObject($data, $className);

        // denormalize the designated properties of the data into an array
        $array = $this->normalizingService->denormalize($data, $className, $properties);
        $result['array'] = $array;
        $result['original'] = $this->getOriginal($entity, $array);

        // update the entity with the values of the denormalized array

        $this->objectManager->updateObject($entity, $array);
        $this->objectManager->mergeObject($entity);
        $result['entity'] = $entity;

        return $result;
    }

    public function getOriginal ($entity, $array)
    {
        $result = [];

        foreach (array_keys($array) as $property) {
            $result[$property] = $this->objectManager->readObject($entity, $property);
        }

        return $result;
    }

}
