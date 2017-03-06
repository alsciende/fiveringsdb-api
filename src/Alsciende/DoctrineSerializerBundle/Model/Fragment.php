<?php

namespace Alsciende\DoctrineSerializerBundle\Model;

/**
 * Fragment of Source: one individual record and the file where it's serialized
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

    function __construct (Source $source, string $path, array $incoming)
    {
        $this->source = $source;
        $this->path = $path;
        $this->incoming = $incoming;
    }

}
