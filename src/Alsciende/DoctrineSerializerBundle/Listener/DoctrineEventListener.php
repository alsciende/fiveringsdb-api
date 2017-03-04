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
        $class = $classMetadata->getName();

        $annotation = $this->reader->getClassAnnotation(new \ReflectionClass($class), 'Alsciende\DoctrineSerializerBundle\Annotation\Source');
        if ($annotation) {
            $this->manager->addSource($class, $annotation, $classMetadata, $args->getEntityManager());
        }
        
    }

}
