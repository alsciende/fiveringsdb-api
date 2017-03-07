<?php

namespace Alsciende\DoctrineSerializerBundle\Model;

/**
 * Represents a data fragment, a record to be serialized/deserialized
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Fragment
{
    
    /* @var Source */
    private $source;
    
    /* @var string */
    private $path;

    /* @var object */
    private $entity;
    
    /* @var array */
    private $original;

    /* @var array */
    private $incoming;

    /* @var array */
    private $changes;

    function __construct (Source $source, $path, $incoming)
    {
        $this->source = $source;
        $this->path = $path;
        $this->incoming = $incoming;
    }

    /**
     * 
     * @return Source
     */
    function getSource ()
    {
        return $this->source;
    }

    /**
     * 
     * @return string
     */
    function getPath ()
    {
        return $this->path;
    }

    /**
     * 
     * @return object
     */
    function getEntity ()
    {
        return $this->entity;
    }

    /**
     * 
     * @return array
     */
    function getOriginal ()
    {
        return $this->original;
    }

    /**
     * 
     * @return array
     */
    function getIncoming ()
    {
        return $this->incoming;
    }

    /**
     * 
     * @return array
     */
    function getChanges ()
    {
        return $this->changes;
    }

    /**
     * 
     * @param Source $source
     * @return Fragment
     */
    function setSource ($source)
    {
        $this->source = $source;
        
        return $this;
    }

    /**
     * 
     * @param string $path
     * @return Fragment
     */
    function setPath ($path)
    {
        $this->path = $path;
        
        return $this;
    }

    /**
     * 
     * @param object $entity
     * @return Fragment
     */
    function setEntity ($entity)
    {
        $this->entity = $entity;
        
        return $this;
    }

    /**
     * 
     * @param array $original
     * @return Fragment
     */
    function setOriginal ($original)
    {
        $this->original = $original;
        
        return $this;
    }

    /**
     * 
     * @param array $incoming
     * @return Fragment
     */
    function setIncoming ($incoming)
    {
        $this->incoming = $incoming;
        
        return $this;
    }

    /**
     * 
     * @param array $changes
     * @return Fragment
     */
    function setChanges ($changes)
    {
        $this->changes = $changes;
        
        return $this;
    }

}
