<?php

namespace Alsciende\SerializerBundle\Model;

/**
 * Represents a data fragment, a record to be serialized/deserialized
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Fragment
{

    /**
     * @var array
     */
    private $data;

    /**
     * @var Block
     */
    private $block;

    function __construct ($data)
    {
        $this->data = $data;
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
