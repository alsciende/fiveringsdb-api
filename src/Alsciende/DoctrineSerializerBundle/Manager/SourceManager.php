<?php

namespace Alsciende\DoctrineSerializerBundle\Manager;

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

    public function __construct ()
    {
        $this->sources = [];
    }

    /**
     * Return all sources
     * 
     * @return array
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
     * @param string $class
     * @param \Alsciende\DoctrineSerializerBundle\Annotation\Source $annotation
     * @param \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
     * @param \Doctrine\ORM\EntityManager  $entityManager
     */
    public function addSource ($class, $annotation, $classMetadata, $entityManager)
    {
        $this->sources[$class] = [
            "class" => $class,
            "annotation" => $annotation,
            "classMetadata" => $classMetadata,
            "entityManager" => $entityManager
        ];
    }

    /**
     * Order Sources by dependancies
     * Sources can only depend on Sources before them in the list
     */
    public function orderSources ()
    {
        $sourcesOrdered = [];
        $classesOrdered = [];
        
        while (count($this->sources)) {
            $next = $this->findNextResolvedSource($this->sources, $classesOrdered);
            if (!$next) {
                throw new \InvalidArgumentException("Data sources contain a cycle of dependencies in [" . implode(", ", array_keys($this->sources)) . "]");
            }
            
            $classesOrdered[] = $next;
            $sourcesOrdered[] = $this->sources[$next];
            unset($this->sources[$next]);
        }
        
        return $sourcesOrdered;
    }

    /**
     * Find the first class in $toDo that only depends on classes in $done
     * 
     * @return string
     */
    public function findNextResolvedSource ($toDo, $done)
    {
        foreach ($toDo as $source) {
            $resolved = $this->allTargetEntitiesAreKnown($source['classMetadata'], $done);
            if($resolved) {
                return $source['class'];
            }
        }
        
        return null;
    }

    /**
     * Return true if all target entities of the association mappings of $classMetadata are listed in $classes
     * 
     * @param \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
     * @param array $classes
     * @return boolean
     */
    public function allTargetEntitiesAreKnown (\Doctrine\ORM\Mapping\ClassMetadata $classMetadata, $classes)
    {
        foreach ($classMetadata->getAssociationMappings() as $mapping) {
            if (!in_array($mapping['targetEntity'], $classes)) {
                return false;
            }
        }
        
        return true;
    }

}
