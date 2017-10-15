<?php

namespace Alsciende\SerializerBundle\Service;

use Alsciende\SerializerBundle\Manager\ObjectManagerInterface;

/**
 * Turns an object into an array
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class NormalizingService implements NormalizingServiceInterface
{

    public function __construct (ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     *
     * @var \Alsciende\SerializerBundle\Manager\ObjectManagerInterface
     */
    private $objectManager;

    /**
     *
     * @param type $entity
     * @param type $propertyMap
     * @return type
     */
    public function toArray ($entity, $propertyMap)
    {
        $result = [];

        foreach ($propertyMap as $property => $type) {
            $result[$property] = $this->objectManager->readObject($entity, $property);
        }

        return $result;
    }

    /**
     * with [ "id" => 3, "name" => "The Dark Side of the Moon", "releasedAt" => (DateTime), "band" => (Band) ]
     * does [ "id" => 3, "name" => "The Dark Side of the Moon", "released_at" => "1973-03-01", "band_code" => "pink-floyd" ]
     *
     * @param type $data
     * @param type $className
     * @param type $propertyMap
     * @return type
     */
    public function normalize ($data, $className, $propertyMap)
    {
        $result = [];

        foreach ($propertyMap as $property => $type) {
            $value = $data[$property];
            if ($value === null) {
                $this->objectManager->setFieldValue($result, $className, $property, null);
                continue;
            }
            switch ($type) {
                case 'string':
                    $this->objectManager->setFieldValue($result, $className, $property, $value);
                    break;
                case 'integer':
                    $this->objectManager->setFieldValue($result, $className, $property, $value);
                    break;
                case 'boolean':
                    $this->objectManager->setFieldValue($result, $className, $property, $value);
                    break;
                case 'date':
                    $value = $value->format('Y-m-d');
                    $this->objectManager->setFieldValue($result, $className, $property, $value);
                    break;
                case 'array':
                    $this->objectManager->setFieldValue($result, $className, $property, $value);
                    break;
                case 'association':
                    $this->objectManager->setAssociationValue($result, $className, $property, $value);
                    break;
                default:
                    throw new \Exception("Unknown type: $type");
            }
        }

        return $result;
    }

    /**
     * with [ "id" => 3, "name" => "The Dark Side of the Moon", "released_at" => "1973-03-01", "band_code" => "pink-floyd" ]
     * does [ "id" => 3, "name" => "The Dark Side of the Moon", "releasedAt" => (DateTime), "band" => (Band) ]
     *
     * @param type $data
     * @param type $className
     * @param type $propertyMap
     * @return type
     */
    public function denormalize ($data, $className, $propertyMap)
    {
        $result = [];

        foreach ($propertyMap as $property => $type) {
            switch ($type) {
                case 'string':
                    $value = $this->objectManager->getFieldValue($data, $className, $property);
                    $result[$property] = $value;
                    break;
                case 'integer':
                    $value = $this->objectManager->getFieldValue($data, $className, $property);
                    $result[$property] = isset($value) ? (integer) $value : null;
                    break;
                case 'boolean':
                    $value = $this->objectManager->getFieldValue($data, $className, $property);
                    $result[$property] = isset($value) ? (boolean) $value : null;
                    break;
                case 'array':
                    $value = $this->objectManager->getFieldValue($data, $className, $property);
                    $result[$property] = $value;
                    break;
                case 'date':
                    $value = $this->objectManager->getFieldValue($data, $className, $property);
                    $result[$property] = $value ? \DateTime::createFromFormat('Y-m-d', $value) : null;
                    break;
                case 'association':
                    $value = $this->objectManager->getAssociationValue($data, $className, $property);
                    $result[$property] = $value;
                    break;
                default:
                    throw new \Exception("Unknown type: $type");
            }
        }

        return $result;
    }

}
