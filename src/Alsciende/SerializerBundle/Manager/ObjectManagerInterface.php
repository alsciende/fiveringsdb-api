<?php

namespace Alsciende\SerializerBundle\Manager;

/**
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface ObjectManagerInterface
{

    /**
     * Returns a list of all classes
     *
     * @return string[]
     */
    function getAllManagedClassNames ();

    /**
     * Returns an array of foreign key => foreign entity class
     * for all classes that this class depends on
     *
     * @param string $className
     * @return string[]
     */
    function getAllTargetClasses ($className);

    /**
     * Returns the single identifier of a class. Throws an exception if the class
     * using a composite key
     *
     * @param string $className
     */
    function getSingleIdentifier ($className);

    /**
     * Updates some fields in the entity
     *
     * @param object $entity
     * @param array $data
     * @return object
     */
    function updateObject ($entity, $data);

    /**
     * Return a managed entity
     *
     * @param object $entity
     * @return object
     */
    function mergeObject ($entity);

    /**
     * Returns the value of a field of the entity
     *
     * @param object $entity
     * @param string $field
     */
    function readObject ($entity, $field);

    /**
     * Find the entity referenced by the identifiers in $data
     *
     * @param array $identifiers
     * @param string $className
     * @return object
     */
    function findObject ($identifiers, $className);

    /**
     * Find the entity referenced by the identifiers in $data, or create a new one with the correct identifiers
     *
     * @param array $data
     * @param string $className
     * @return object
     */
    function findOrCreateObject ($data, $className);

    /**
     * Returns the array of identifier keys/values that can be used with find()
     * to find the entity described by $incoming
     *
     * If an identifier is a foreignIdentifier, find the foreign entity
     *
     * @return array
     */
    function getIdentifierValues ($data, $className);

    /**
     * Finds all the foreign keys in $data and the entity associated
     *
     * eg ["article_id" => 2134] returns
     * array([ "associationKey" => "article", "associationValue" => (object Article), "referenceKeys" => [ "article_id"] ])
     *
     * @return array
     */
    function findAssociations ($data, $className);

    function getFieldValue ($data, $className, $fieldName);

    function getAssociationValue ($data, $className, $fieldName);

    /**
     * Returns the class name of an entity, even if the object is a Proxy
     * @param object $entity
     */
    function getClassName ($entity);
}
