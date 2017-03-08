<?php

namespace Alsciende\DoctrineSerializerBundle\Model;

/**
 * Represents a Block of data: some encoded text at a path, that must be decoded
 * as a list of Fragments
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Block
{

    /**
     * @var Source
     */
    private $source;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $data;

    /**
     * 
     * @param string $data
     * @param string $path
     */
    function __construct ($data, $path = null)
    {
        $this->data = $data;
        $this->path = $path;
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
     * @return text
     */
    function getPath ()
    {
        return $this->path;
    }

    /**
     * 
     * @return text
     */
    function getData ()
    {
        return $this->data;
    }

    /**
     * 
     * @param \Alsciende\DoctrineSerializerBundle\Model\Source $source
     * @return Block
     */
    function setSource (Source $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * 
     * @param string $path
     * @return Source
     */
    function setPath ($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * 
     * @param string $data
     * @return Source
     */
    function setData ($data)
    {
        $this->data = $data;

        return $this;
    }

}
