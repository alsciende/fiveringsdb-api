<?php

namespace Alsciende\DoctrineSerializerBundle;

/**
 * Description of Serializer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Serializer
{
    /* @var \Doctrine\ORM\EntityManager */
    private $entityManager;
    
    /* @var Manager\SourceManager */
    private $sourceManager;
    
    /* @var \Symfony\Component\Validator\Validator\RecursiveValidator */
    private $validator;
    
    public function __construct (\Doctrine\ORM\EntityManager $entityManager, Manager\SourceManager $sourceManager, \Symfony\Component\Validator\Validator\RecursiveValidator $validator)
    {
        $this->entityManager = $entityManager;
        $this->sourceManager = $sourceManager;
        $this->validator = $validator;
    }
    
    public function import()
    {
     
        /* @var $encoder JsonFileEncoder */
        $encoder = new JsonFileEncoder();

        $this->entityManager->getMetadataFactory()->getAllMetadata();
        $sources = $this->sourceManager->getSources();

        $allJobs = [];
        
        foreach($sources as $source) {

            $files = $encoder->decode($source);
            
            $jobs = [];
            foreach($files as $file) {
                $jobs[] = new DeserializationJob($file[0], $file[1], $source);
            }

            foreach ($jobs as $job) {
                $job->run($source->entityManager, $this->validator);
            }
            
            $source->entityManager->flush();
            
            $allJobs = array_merge($allJobs, $jobs);
        }
        
        return $allJobs;
    }
    
}
