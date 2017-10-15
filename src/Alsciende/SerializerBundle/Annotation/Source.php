<?php

namespace Alsciende\SerializerBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Source annotation
 *
 * @Annotation
 * @Target({"CLASS","PROPERTY"})
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Source extends Annotation
{

    /* @var string */
    public $break;

    /* @var string */
    public $path;

    /* @var string */
    public $type;
}
