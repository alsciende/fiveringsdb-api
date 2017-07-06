<?php

namespace Alsciende\SerializerBundle\Annotation;

/**
 * @Source annotation
 *
 * @Annotation
 * @Target({"CLASS","PROPERTY"})
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Source extends \Doctrine\Common\Annotations\Annotation
{

    /* @var string */
    public $break;

    /* @var string */
    public $path;

    /* @var string */
    public $type;
}
