<?php

namespace Alsciende\SerializerBundle\Model;

/**
 * Represents a data fragment, a record to be serialized/deserialized
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Fragment
{
    
    /* @var array */
    private $data;

    /* @var Block */
    private $block;

    /* @var object */
    private $entity;
    
    /* @var array */
    private $original;

    /* @var array */
    private $changes;

    function __construct ($data)
    {
        $this->data = $data;
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
    function getData ()
    {
        return $this->data;
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
     * @param array $data
     * @return Fragment
     */
    function setData ($data)
    {
        $this->data = $data;
        
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

    /**
     * 
     * @return Block
     */
    function getBlock ()
    {
        return $this->block;
    }

    /**
     * 
     * @param Block $block
     * @return Fragment
     */
    function setBlock ($block)
    {
        $this->block = $block;
        
        return $this;
    }


}
