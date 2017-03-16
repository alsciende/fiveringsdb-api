<?php

namespace Alsciende\SerializerBundle\Service;

/**
 * Merges objects one into the other
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class MergingService
{
    public function __construct (\Alsciende\SerializerBundle\Manager\Entity\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }
    
    /**
     *
     * @var \Alsciende\SerializerBundle\Manager\ObjectManagerInterface
     */
    private $objectManager;
    
    public function merge($dest, ...$objects)
    {
        
    }
    
}
