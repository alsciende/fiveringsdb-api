<?php

namespace Alsciende\SerializerBundle\Service;

/**
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface NormalizingServiceInterface
{
    public function normalize($object);
    public function denormalize($data, $className, $propertyMap);
}
