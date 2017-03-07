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
     * Returns an array of foreign key => foreign entity class 
     * for all classes that this class depends on
     * 
     * @param string $className
     * @return string[]
     */
    function getDependingClassNames($className);

    /**
     * Returns the single identifier of a class. Throws an exception if the class
     * using a composite key
     * 
     * @param string $className
     */
    function getSingleIdentifier ($className);
    
    /**
     * Commits all modifications made to managed entities
     */
    function flush();
    
    /**
     * Updates some fields in the entity
     * 
     * @param object $entity
     * @param array $update
     */
    function updateEntity($entity, $update);

    /**
     * Returns the value of a field of the entity
     * 
     * @param object $entity
     * @param string $field
     */
    function readEntity($entity, $field);
    
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
