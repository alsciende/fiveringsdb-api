<?php

namespace Alsciende\DoctrineSerializerBundle\Annotation;

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
    /**
     *
     * @var boolean
     */
    public $multipleFiles;
    
    /**
     *
     * @var boolean
     */
    public $multipleRecords;
    
    /**
     *
     * @var string
     */
    public $groupBy;
    
    /**
     * 
     * @return boolean
     */
    function isMultipleFiles ()
    {
        return $this->multipleFiles;
    }

    /**
     * 
     * @return boolean
     */
    function isMultipleRecords ()
    {
        return $this->multipleRecords;
    }

    /**
     * 
     * @return string
     */
    function getGroupBy ()
    {
        return $this->groupBy;
    }


}
