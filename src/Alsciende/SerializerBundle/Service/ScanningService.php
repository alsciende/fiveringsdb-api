<?php

namespace Alsciende\SerializerBundle\Service;

use Alsciende\SerializerBundle\Manager\ObjectManagerInterface;
use Alsciende\SerializerBundle\Model\Source;
use ReflectionClass;
use ReflectionProperty;

/**
 * This Service finds all Sources declared in the application
 *
 * @author Cédric Bertolini <cedric.bertolini@proximedia.fr>
 */
class ScanningService
{

    /**
     * @var ObjectManagerInterface 
     */
    private $objectManager;

    /**
     * @var SourceOrderingService
     */
    private $orderingService;

    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $reader;

    /**
     * @var string
     */
    private $path;

    public function __construct (ObjectManagerInterface $objectManager, SourceOrderingService $orderingService, \Doctrine\Common\Annotations\Reader $reader, $path)
    {
        $this->objectManager = $objectManager;
        $this->orderingService = $orderingService;
        $this->reader = $reader;
        $this->path = $path;
    }

    /**
     * 
     * @return Source[]
     */
    public function findSources ()
    {
        $sources = [];

        foreach($this->objectManager->getAllManagedClassNames() as $className) {
            $reflectionClass = new ReflectionClass($className);
            $annotation = $this->reader->getClassAnnotation($reflectionClass, \Alsciende\SerializerBundle\Annotation\Source::class);
            if($annotation) {
                $sources[] = $this->buildSource($annotation, $reflectionClass);
            }
        }

        return $this->orderingService->orderSources($sources);
    }

    protected function buildSource (\Alsciende\SerializerBundle\Annotation\Source $annotation, \ReflectionClass $reflectionClass)
    {
        $path = $annotation->path ?: $this->path;
        $source = new Source($reflectionClass->getName(), $path, $annotation->break);
        
        /* @var $reflectionProperty ReflectionProperty */
        foreach($reflectionClass->getProperties() as $reflectionProperty) {
            $annotation = $this->reader->getPropertyAnnotation($reflectionProperty, \Alsciende\SerializerBundle\Annotation\Source::class);
            if($annotation) {
                $source->addProperty($reflectionProperty->name, $annotation->type);
            }
        }
        return $source;
    }

}
