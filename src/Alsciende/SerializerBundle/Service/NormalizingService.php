<?php

namespace Alsciende\SerializerBundle\Service;

/**
 * Turns an object into an array
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class NormalizingService implements NormalizingServiceInterface
{

    public function __construct (\Alsciende\SerializerBundle\Manager\ObjectManagerInterface $objectManager)
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
     * @var \Symfony\Component\Serializer\Serializer
     */
    private $serializer;

    public function normalize ($object)
    {
        $data = $this->serializer->normalize($object);

        return $data;
    }

    public function denormalize ($data, $className, $propertyMap)
    {
        $result = [];
        
        foreach($propertyMap as $fieldName => $type) {
            switch($type) {
                case 'string':
                    $columnName = $this->objectManager->getColumnName($className, $fieldName);
                    $result[$fieldName] = $data[$columnName];
                    break;
                case 'integer':
                    $columnName = $this->objectManager->getColumnName($className, $fieldName);
                    $result[$fieldName] = (integer) $data[$columnName];
                    break;
                case 'date':
                    $columnName = $this->objectManager->getColumnName($className, $fieldName);
                    $result[$fieldName] = \DateTime::createFromFormat('Y-m-d', $data[$columnName]);
                    break;
                case 'association':
                    //throw new \Exception("Association denormalization not yet implemented!");
                    break;
            }
        }
        
        return $result;
    }

}
