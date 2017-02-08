<?php

namespace Alsciende\CerealBundle;

/**
 * Description of RepositorySerializer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Cereal
{
    /** @var \Doctrine\ORM\EntityManager */
    private $em;
    
    /** @var JsonFileEncoder */
    private $encoder;
    
    /** @var AssociationNormalizer */
    private $normalizer;
            
    /** @var string */
    private $jsonDataPath;
    
    /** @var string */
    private $className;
    
    function __construct (\Doctrine\ORM\EntityManager $em, $jsonDataPath, $className)
    {
        $this->em = $em;
        $this->jsonDataPath = $jsonDataPath;
        $this->className = $className;

        $this->encoder = new JsonFileEncoder();
        $this->normalizer = new AssociationNormalizer($em);
    }

    /**
     * 
     * @return array
     */
    public function import()
    {
        $arrays = $this->encoder->decode($this->jsonDataPath, $this->className);
        
        $entities = [];
        foreach($arrays as $array) {
            $entities[] = $this->normalizer->denormalize($array, $this->className);
        }
        
        return $entities;
    }
    
    public function export()
    {
        
    }
    
}
