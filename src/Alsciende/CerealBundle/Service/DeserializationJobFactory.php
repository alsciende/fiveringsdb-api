<?php

namespace Alsciende\CerealBundle\Service;

/**
 * Description of DeserializationJobFactory
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeserializationJobFactory
{

    /** @var Alsciende\CerealBundle\Service\JsonFileEncoder */
    private $encoder;
    
    function __construct ()
    {
        $this->encoder = new JsonFileEncoder();
    }
    
    function create($jsonDataPath, $classname)
    {
        $jobs = [];
        
        $decodeJsonFiles = $this->encoder->decode($jsonDataPath, $classname);
        foreach($decodeJsonFiles as $decodedJsonFile) {
            $jobs[] = new \Alsciende\CerealBundle\Model\DeserializationJob($decodedJsonFile, $classname);
        }
        
        return $jobs;
    }
    
    

}
