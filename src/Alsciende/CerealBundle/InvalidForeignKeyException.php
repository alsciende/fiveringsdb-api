<?php

namespace Alsciende\CerealBundle;

/**
 * Description of InvalidForeignKeyException
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class InvalidForeignKeyException extends \Exception
{

    /** @var array */
    public $invalidForeignKeys;

    /** @var array */
    public $decodedData;

    function __construct ($decodedData, $invalidForeignKeys, $message = '')
    {
        $this->decodedData = $decodedData;
        $this->invalidForeignKeys = $invalidForeignKeys;
        parent::__construct($message);
    }

    function getDecodedData ()
    {
        return $this->decodedData;
    }

    function setDecodedData ($decodedData)
    {
        $this->decodedData = $decodedData;
    }

    function getInvalidForeignKeys ()
    {
        return $this->invalidForeignKeys;
    }

    function setInvalidForeignKeys ($invalidForeignKeys)
    {
        $this->invalidForeignKeys = $invalidForeignKeys;
    }

}
