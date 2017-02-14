<?php

namespace Alsciende\CerealBundle\Service;

/**
 * Description of RepositorySerializer
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Cereal
{

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /** @var  \Symfony\Component\Validator\Validator\RecursiveValidator */
    private $validator;

    /** @var JsonFileEncoder */
    private $encoder;

    /** @var AssociationNormalizer */
    private $normalizer;

    function __construct (\Doctrine\ORM\EntityManager $em, \Symfony\Component\Validator\Validator\RecursiveValidator $validator)
    {
        $this->em = $em;
        $this->validator = $validator;

        $this->encoder = new JsonFileEncoder();
        $this->normalizer = new AssociationNormalizer($em);
    }

    /**
     * 
     * @return array
     */
    public function import ($jsonDataPath, $className)
    {
        $arrays = $this->encoder->decode($jsonDataPath, $className);

        $entities = [];
        foreach($arrays as $array) {
            $entities[] = $this->normalizer->denormalize($array, $className);
        }

        foreach($entities as $entity) {
            $errors = $this->validator->validate($entity);
            if(count($errors) > 0) {
                $errorsString = (string) $errors;
                throw new \Exception($errorsString);
            }
            $this->em->merge($entity);
        }

        $this->em->flush();

        return $entities;
    }

    public function export ($jsonDataPath, $className)
    {
        
        $repository = $this->em->getRepository($className);
        if(!$repository) {
            throw new \Exception("Cannot find repository for class $className");
        }
        /*
        $list = $repository->findAll();
        dump($list);
         * 
         */
    }

}
