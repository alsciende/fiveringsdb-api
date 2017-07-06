<?php

namespace Alsciende\SerializerBundle\Service;

/**
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface NormalizingServiceInterface
{
    /**
     *
     * @param type $entity
     * @param type $propertyMap
     */
    public function toArray ($entity, $propertyMap);

    /**
     * Returns a new array with all the key/value pairs in data normalized
     * according to the property names and class annotations of the type
     *
     * @param array $data The data to normalize
     * @param string $className The type of the data
     * @param array $propertyMap An array mapping property names to their types
     */
    public function normalize ($data, $className, $propertyMap);

    /**
     * Returns a new array with all the key/value pairs in data denormalized
     * according to the property names and class annotations of the type
     *
     * @param array $data The data to denormalize
     * @param string $className The type of the data
     * @param array $propertyMap An array mapping property names to their types
     */
    public function denormalize ($data, $className, $propertyMap);
}
