<?php

namespace Alsciende\DoctrineSerializerBundle\Model;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Source Annotation for the bundle
 * 
 * @Annotation
 * @Target("CLASS")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Source extends Annotation
{
    
    /* @var string */
    public $break;

    /* @var string */
    public $group;

    /* @var string */
    public $path;

    /* @var string */
    public $className;

    /* @var \Doctrine\ORM\EntityManager */
    public $entityManager;

    /* @var Doctrine\ORM\Mapping\ClassMetadata */
    public $classMetadata;

}
