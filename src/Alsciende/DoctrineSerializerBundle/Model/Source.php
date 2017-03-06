<?php

namespace Alsciende\DoctrineSerializerBundle\Model;

/**
 * Represents a data source, a table to be serialized/deserialized
 * 
 * @Annotation
 * @Target("CLASS")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Source extends \Doctrine\Common\Annotations\Annotation
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
