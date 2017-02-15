<?php

namespace Alsciende\CerealBundle\Exception;

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

    /** @var string * */
    public $className;

    function __construct ($decodedData, $invalidForeignKeys, $className)
    {
        $this->invalidForeignKeys = $invalidForeignKeys;
        $message = "Object($className):\n    These foreign keys have invalid values:";
        foreach($invalidForeignKeys as $key) {
            $message .= "\n    - $key => " . $decodedData[$key];
        }
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

    function getClassName ()
    {
        return $this->className;
    }

    function setClassName ($className)
    {
        $this->className = $className;
    }

}
