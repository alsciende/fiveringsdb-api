<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Alsciende\SerializerBundle\Service;

/**
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface ReferencingServiceInterface
{
    /**
     * Returns an array containing all the references in $object
     * 
     * @param type $object
     * @return $array;
     */
    public function reference($object);
    
    /**
     * Returns an object containing all the associations in $data
     * 
     * @param array $data
     * @param string $type
     * @return object
     */
    public function dereference($data, $type);
}
