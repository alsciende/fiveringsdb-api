<?php

namespace Alsciende\SerializerBundle\Model;

/**
 * Represents a data block: some encoded text at a path,
 *      representing one or more objects of the same class,
 *      that must be decoded as a list of Fragments
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
    private $name;

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
        if (isset($path)) {
            $this->name = pathinfo($path, PATHINFO_FILENAME);
        }
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
     * @return string
     */
    function getData ()
    {
        return $this->data;
    }

    /**
     *
     * @return string
     */
    function getName ()
    {
        return $this->name;
    }

    /**
     *
     * @param \Alsciende\SerializerBundle\Model\Source $source
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

    /**
     *
     * @param string $name
     * @return Source
     */
    function setName ($name)
    {
        $this->name = $name;

        return $this;
    }


}
