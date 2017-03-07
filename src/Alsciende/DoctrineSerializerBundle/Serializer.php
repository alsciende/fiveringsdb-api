<?php

namespace Alsciende\DoctrineSerializerBundle;

/**
 * Description of Serializer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Serializer
{

    /* @var Manager\SourceManager */
    private $sourceManager;

    /* @var \Symfony\Component\Validator\Validator\RecursiveValidator */
    private $validator;

    /* @var AssociationNormalizer */
    private $normalizer;

    /* @var \Doctrine\Common\Annotations\Reader */
    private $reader;

    /* @var Manager\ReferenceManagerInterface */
    private $referenceManager;

    public function __construct (Manager\ReferenceManagerInterface $referenceManager, Manager\SourceManager $sourceManager, \Symfony\Component\Validator\Validator\RecursiveValidator $validator, \Doctrine\Common\Annotations\Reader $reader, \Alsciende\DoctrineSerializerBundle\AssociationNormalizer $normalizer)
    {
        $this->referenceManager = $referenceManager;
        $this->sourceManager = $sourceManager;
        $this->validator = $validator;
        $this->reader = $reader;
        $this->normalizer = $normalizer;
    }

    /**
     * 
     * @return Model\Fragment[]
     */
    public function import ()
    {

        /* @var $encoder JsonFileEncoder */
        $encoder = new JsonFileEncoder();

        $classNames = $this->referenceManager->getAllManagedClassNames();
        foreach($classNames as $className) {
            /* @var $annotation \Alsciende\DoctrineSerializerBundle\Annotation\Source */
            $annotation = $this->reader->getClassAnnotation(new \ReflectionClass($className), 'Alsciende\DoctrineSerializerBundle\Annotation\Source');
            if ($annotation) {
                $source = new Model\Source($className, $annotation->path, $annotation->break, $annotation->group);
                $this->sourceManager->addSource($source);
            }
        }
        $sources = $this->sourceManager->getSources();

        $result = [];

        foreach ($sources as $source) {

            $fragments = $encoder->decode($source);

            foreach ($fragments as $fragment) {
                $this->importFragment($fragment);
            }

            $this->referenceManager->flush();

            $result = array_merge($result, $fragments);
        }

        return $result;
    }

    /**
     * 
     * @param \Alsciende\DoctrineSerializerBundle\Model\Fragment $fragment
     * @throws \Exception
     */
    public function importFragment (Model\Fragment $fragment)
    {
        // find the entity based on the incoming identifier
        $fragment->setEntity($this->referenceManager->findEntity($fragment->getSource()->getClassName(), $fragment->getIncoming()));

        // normalize the entity in its original state
        $fragment->setOriginal($this->normalizer->normalize($fragment->getEntity(), $fragment->getSource()->getGroup()));

        // compute changes between the normalized data
        $fragment->setChanges(array_diff($fragment->getIncoming(), $fragment->getOriginal()));

        // denormalize the associations in the incoming data
        $references = $this->referenceManager->findReferences($fragment->getSource()->getClassName(), $fragment->getIncoming());
        $findForeignKeyValues = $this->normalizer->findForeignKeyValues($references);

        $renamedKeys = [];
        $incoming = $fragment->getIncoming();
        
        // replace the references with foreignKeys
        foreach($findForeignKeyValues as $findForeignKeyValue) {
            foreach($findForeignKeyValue['joinColumns'] as $joinColumn) {
                unset($incoming[$joinColumn]);
                $renamedKeys[$joinColumn] = $findForeignKeyValue['foreignKey'];
            }
            $incoming[$findForeignKeyValue['foreignKey']] = $findForeignKeyValue['foreignValue'];
        }
        
        // update the entity with the field updated in incoming
        $updatedFields = [];
        foreach ($fragment->getChanges() as $field => $value) {
            if (isset($renamedKeys[$field])) {
                $field = $renamedKeys[$field];
                $value = $incoming[$field];
            }
            $updatedFields[$field] = $value;
        }
        $this->referenceManager->updateEntity($fragment->getEntity(), $updatedFields);
        
        $fragment->setIncoming($incoming);

        $errors = $this->validator->validate($fragment->getEntity());
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            throw new \Exception($errorsString);
        }
    }


}
