<?php

namespace Alsciende\SerializerBundle\Serializer;

use Alsciende\SerializerBundle\Service\NormalizingServiceInterface;
use Alsciende\SerializerBundle\Service\ScanningService;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Description of Deserializer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Deserializer
{

    public function __construct (ScanningService $scanningService, NormalizingServiceInterface $normalizingService)
    {
        $this->scanningService = $scanningService;
        $this->normalizingService = $normalizingService;

        $encoders = array();
        $normalizers = array(new ObjectNormalizer());
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @var ScanningService
     */
    private $scanningService;

    /**
     * @var NormalizingServiceInterface
     */
    private $normalizingService;

    /**
     * @var Serializer
     */
    private $serializer;

    public function deserialize ($entity)
    {
        $source = $this->scanningService->buildFromEntity($entity);
        $data = $this->normalizingService->toArray($entity, $source->getProperties());
        return $this->normalizingService->normalize($data, $source->getClassName(), $source->getProperties());
    }

}
