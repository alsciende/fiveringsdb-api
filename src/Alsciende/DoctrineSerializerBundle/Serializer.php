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
    
    /* @var string */
    private $path;
    
    public function __construct (\Doctrine\ORM\EntityManager $entityManager, Manager\SourceManager $sourceManager, \Symfony\Component\Validator\Validator\RecursiveValidator $validator, $path)
    {
        $this->entityManager = $entityManager;
        $this->sourceManager = $sourceManager;
        $this->validator = $validator;
        $this->path = $path;
    }
    
    public function import()
    {
     
        /* @var $encoder JsonFileEncoder */
        $encoder = new JsonFileEncoder();

        $this->entityManager->getMetadataFactory()->getAllMetadata();
        $sources = $this->sourceManager->getSources();

        $allJobs = [];
        
        foreach($sources as $source) {
            /* @var $annotation Annotation\Source */
            $annotation = $source['annotation'];
            
            /* @var $entityManager \Doctrine\ORM\EntityManager */
            $entityManager = $source['entityManager'];

            $files = $encoder->decode($this->path, $source['class'], $annotation->isMultipleFiles(), $annotation->isMultipleRecords());
            
            $jobs = [];
            foreach($files as $file) {
                $jobs[] = new DeserializationJob($file[0], $file[1], $source['class']);
            }

            foreach ($jobs as $job) {
                $job->run($entityManager, $this->validator);
            }
            
            $entityManager->flush();
            
            $allJobs = array_merge($allJobs, $jobs);
        }
        
        return $allJobs;
    }
    
}
