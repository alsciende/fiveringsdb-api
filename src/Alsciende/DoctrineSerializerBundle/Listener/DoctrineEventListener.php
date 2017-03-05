<?php

namespace Alsciende\DoctrineSerializerBundle\Listener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * Description of DoctrineEventListener
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DoctrineEventListener
{
    /* @var \Doctrine\Common\Annotations\Reader */
    private $reader;
    
    /* @var Alsciende\DoctrineSerializerBundle\Sources\SourceManager */
    private $manager;
    
    public function __construct (\Doctrine\Common\Annotations\Reader $reader, \Alsciende\DoctrineSerializerBundle\Manager\SourceManager $manager)
    {
        $this->reader = $reader;
        $this->manager = $manager;
    }

    public function loadClassMetadata (LoadClassMetadataEventArgs $args)
    {
        $classMetadata = $args->getClassMetadata();
        $className = $classMetadata->getName();

        /* @var $source \Alsciende\DoctrineSerializerBundle\Model\Source */
        $source = $this->reader->getClassAnnotation(new \ReflectionClass($className), 'Alsciende\DoctrineSerializerBundle\Model\Source');
        if ($source) {
            $source->classMetadata = $classMetadata;
            $source->entityManager = $args->getEntityManager();
            $source->className = $className;
            $this->manager->addSource($source);
        }
        
    }

}
