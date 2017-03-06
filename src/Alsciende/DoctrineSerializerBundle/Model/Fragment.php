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
    public $source;
    
    /* @var string */
    public $path;

    /* @var object */
    public $entity;
    
    /* @var array */
    public $original;

    /* @var array */
    public $incoming;

    /* @var array */
    public $changes;

    function __construct (Source $source, $path, $incoming)
    {
        $this->source = $source;
        $this->path = $path;
        $this->incoming = $incoming;
    }

}
