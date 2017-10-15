<?php

namespace Alsciende\SerializerBundle\Service;

use Alsciende\SerializerBundle\Manager\ObjectManagerInterface;
use Alsciende\SerializerBundle\Model\Source;
use Doctrine\Common\Annotations\Reader;
use Psr\Cache\CacheItemPoolInterface;
use ReflectionClass;
use ReflectionProperty;

/**
 * This Service finds all Sources declared in the application
 *
 * @author Alsciende <alsciende@icloud.com>
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
     * @var \Psr\Cache\CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $path;

    public function __construct (
        ObjectManagerInterface $objectManager,
        SourceOrderingService $orderingService,
        Reader $reader,
        CacheItemPoolInterface $cache,
        $path
    ) {
        $this->objectManager = $objectManager;
        $this->orderingService = $orderingService;
        $this->reader = $reader;
        $this->cache = $cache;
        $this->path = $path;
    }

    /**
     *
     * @return Source[]
     */
    public function findSources ()
    {
        $sources = [];

        foreach ($this->objectManager->getAllManagedClassNames() as $className) {
            if ($source = $this->buildFromClass($className)) {
                $sources[] = $source;
            }
        }

        return $this->orderingService->orderSources($sources);
    }

    /**
     *
     * @param object $entity
     * @return Source
     */
    public function buildFromEntity ($entity)
    {
        $className = $this->objectManager->getClassName($entity);
        return $this->buildFromClass($className);
    }

    /**
     *
     * @param string $className
     * @return Source
     */
    public function buildFromClass ($className)
    {
        $cacheKey = $this->getCacheKey($className);
        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $reflectionClass = new ReflectionClass($className);
        $annotation = $this->reader->getClassAnnotation($reflectionClass, \Alsciende\SerializerBundle\Annotation\Source::class);
        if ($annotation instanceof \Alsciende\SerializerBundle\Annotation\Source) {
            $source = $this->buildSource($annotation, $reflectionClass);
            $cacheItem->set($source);
            return $source;
        }
    }

    protected function buildSource (\Alsciende\SerializerBundle\Annotation\Source $annotation, \ReflectionClass $reflectionClass)
    {
        $path = $annotation->path ?: $this->path;
        $source = new Source($reflectionClass->getName(), $path, $annotation->break);

        /* @var $reflectionProperty ReflectionProperty */
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $annotation = $this->reader->getPropertyAnnotation($reflectionProperty, \Alsciende\SerializerBundle\Annotation\Source::class);
            if ($annotation) {
                $source->addProperty($reflectionProperty->name, $annotation->type);
            }
        }
        return $source;
    }

    protected function getCacheKey ($className)
    {
        return "alsciende_serializer.source." . strtr($className, '\\', '_');
    }

}
