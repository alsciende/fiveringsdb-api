<?php

namespace Alsciende\CerealBundle\Model;

/**
 * Description of DecodedJsonFile
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DecodedJsonFile
{
    
    /* @var string */
    private $filepath;

    /* @var array */
    private $data;

    function __construct ($filepath, $data)
    {
        $this->filepath = realpath($filepath);
        $this->data = $data;
    }

    function getFilepath ()
    {
        return $this->filepath;
    }

    function getData ()
    {
        return $this->data;
    }

}
