<?php

namespace Alsciende\DoctrineSerializerBundle\Manager;

/**
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface ReferenceManagerInterface
{
    /**
     * Returns a list of all classes that this class depends on
     * 
     * @param string $className
     * @return string[]
     */
    function getClassDependencies($className);

}
