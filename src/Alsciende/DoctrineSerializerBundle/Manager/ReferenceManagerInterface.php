<?php

namespace Alsciende\DoctrineSerializerBundle\Manager;

/**
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface ReferenceManagerInterface
{
    /**
     * Returns a list of all classes
     * 
     * @return string[]
     */
    function getAllManagedClassNames();

    
    /**
     * Returns a list of all classes that this class depends on
     * 
     * @param string $className
     * @return string[]
     */
    function getDependingClassNames($className);

    /**
     * Commits all modifications made to managed entities
     */
    function flush();
    
    /**
     * Updates a field in the entity
     * 
     * @param object $entity
     * @param array $update
     */
    function updateEntity($entity, $update);
    
    /**
     * Find the entity referenced by the identifiers in $data
     * @param string $className
     * @param array $data
     * @return object
     */
    function findEntity ($className, $data);
    
    /**
     * Return an array listing the associations in $metadata that exist in $data,
     * with the relevant information to find the referenced entity
     * 
     * @param string className
     * @param array $data
     * @return array
     */
    function findReferences ($className, $data);
    
    /**
     * Finds the entity described by $reference. $field is a unique identifier.
     * 
     * @param type $field
     * @param type $reference
     * @return object
     */
    function findReferencedEntity ($field, $reference);
    
}
