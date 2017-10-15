<?php

namespace Alsciende\SerializerBundle\Manager;

use Alsciende\SerializerBundle\Model\Source;

/**
 * Description of SourceManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class SourceManager
{

    /* @var array */
    private $sources;

    /* @var array */
    private $ordered;

    /* @var ObjectManagerInterface */
    private $objectManager;

    private $path;

    public function __construct (ObjectManagerInterface $objectManager, $path)
    {
        $this->objectManager = $objectManager;
        $this->path = $path;

        $this->sources = [];
    }

    /**
     * Return all sources
     *
     * @return \Alsciende\SerializerBundle\Model\Source[]
     */
    public function getSources ()
    {
        if (!isset($this->ordered)) {
            $this->ordered = $this->orderSources();
        }

        return $this->ordered;
    }

    /**
     * Add one source
     *
     * @param Source $source
     */
    public function addSource (Source $source)
    {
        if ($source->getPath() === null) {
            $source->setPath($this->path);
        }
        $this->sources[] = $source;
    }

    /**
     * Order Sources by dependencies
     * Sources can only depend on Sources before them in the list
     */
    public function orderSources ()
    {
        $sources = [];
        $classes = [];

        while (count($this->sources)) {
            $next = $this->findNextResolvedSource($this->sources, $classes);
            if ($next === null) {
                $unresolvedClasses = array_map(function (Source $source) {
                    return $source->getClassName();
                }, $this->sources);
                throw new \InvalidArgumentException("Sources contain a cycle of dependencies, or a dependency is not configured as a Source.\nUnresolved classes are: " . implode(", ", $unresolvedClasses) . ".\nResolved classes are : " . implode(", ", $classes) . ".");
            }

            $source = $this->sources[$next];
            $sources[] = $source;
            $classes[] = $source->getClassName();
            array_splice($this->sources, $next, 1);
        }

        return $sources;
    }

    /**
     * Find the first class in $sources that only depends on classes in $classes
     *
     * @param \Alsciende\SerializerBundle\Model\Source[] $sources
     * @param string[] $classes
     * @return integer
     */
    public function findNextResolvedSource ($sources, $classes)
    {
        foreach ($sources as $index => $source) {
            $resolved = $this->allTargetEntitiesAreKnown($source->getClassName(), $classes);
            if ($resolved) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Return true if all target entities of $className are listed in $classes
     *
     * @param string $className
     * @param string[] $classes
     * @return boolean
     */
    public function allTargetEntitiesAreKnown ($className, $classes)
    {
        $dependencies = $this->objectManager->getAllTargetClasses($className);
        foreach (array_values($dependencies) as $dependency) {
            if (!in_array($dependency, $classes)) {
                return false;
            }
        }

        return true;
    }

}
