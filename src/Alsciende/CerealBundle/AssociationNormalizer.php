<?php

namespace Alsciende\CerealBundle;

/**
 * Description of AssociationNormalizer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class AssociationNormalizer
{

    /** @var \Doctrine\ORM\Mapping\ClassMetadataFactory */
    private $factory;

    /** @var \Symfony\Component\Serializer\Serializer */
    private $serializer;

    function __construct (\Doctrine\ORM\EntityManager $em)
    {
        $this->factory = new \Doctrine\ORM\Mapping\ClassMetadataFactory();
        $this->factory->setEntityManager($em);
        
        $classMetadataFactory = new \Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory(new \Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader(new \Doctrine\Common\Annotations\AnnotationReader()));
        $normalizer = new \Symfony\Component\Serializer\Normalizer\ObjectNormalizer($classMetadataFactory);
        $this->serializer = new \Symfony\Component\Serializer\Serializer(array($normalizer));
    }

    /**
     * Finds all the associations in $metadata and add them to $data as a 
     * foreign key
     * 
     * eg "article" => (object Article) becomes "article_id" => 2134
     * 
     */
    function normalize ($entity)
    {
        $metadata = $this->factory->getMetadataFor(get_class($entity));
        $data = $this->serializer->normalize($entity, null, ['groups' => ['json']]);

        foreach($metadata->getAssociationMappings() as $mapping) {
            $targetMetadata = $this->factory->getMetadataFor($mapping['targetEntity']);
            $identifier = $this->getSingleIdentifier($targetMetadata);
            $target = $metadata->getFieldValue($entity, $mapping['fieldName']);
            if($target === null) {
                $value = null;
            } else {
                $value = $targetMetadata->getFieldValue($target, $identifier);
            }
            $compositeField = $mapping['fieldName'] . '_' . $identifier;
            $data[$compositeField] = $value;
        }

        return $data;
    }

    function getSingleIdentifier (\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $identifierFieldNames = $metadata->getIdentifierFieldNames();
        if(count($identifierFieldNames) > 1) {
            throw new InvalidArgumentException('Too many identifiers for ' . $metadata->getName());
        }
        return $identifierFieldNames[0];
    }

}
