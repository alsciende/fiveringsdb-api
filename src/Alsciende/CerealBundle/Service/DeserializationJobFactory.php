<?php

namespace Alsciende\CerealBundle\Service;

/**
 * Description of DeserializationJobFactory
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeserializationJobFactory
{

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /** @var  \Symfony\Component\Validator\Validator\RecursiveValidator */
    private $validator;

    /** @var Alsciende\CerealBundle\Service\JsonFileEncoder */
    private $encoder;
    
    function __construct (\Doctrine\ORM\EntityManager $em, \Symfony\Component\Validator\Validator\RecursiveValidator $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->encoder = new JsonFileEncoder();
    }
    
    function create($jsonDataPath, $classname)
    {
        $jobs = [];
        
        $decodeJsonFiles = $this->encoder->decode($jsonDataPath, $classname);
        foreach($decodeJsonFiles as $decodedJsonFile) {
            $jobs[] = new \Alsciende\CerealBundle\Model\DeserializationJob($this->em, $this->validator, $decodedJsonFile, $classname);
        }
        
        return $jobs;
    }
    
    

}
