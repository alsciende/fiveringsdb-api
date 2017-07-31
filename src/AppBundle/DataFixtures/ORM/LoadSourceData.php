<?php

namespace AppBundle\DataFixtures\ORM;

use Alsciende\SerializerBundle\Serializer\Serializer;
use Alsciende\SerializerBundle\Service\ScanningService;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * Description of LoadSourceData
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LoadSourceData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    use  ContainerAwareTrait;
    
    public function load (ObjectManager $manager)
    {
        /* @var $scanningService ScanningService */
        $scanningService = $this->container->get('alsciende_serializer.scanning_service');

        $sources = $scanningService->findSources();

        /* @var $serializer Serializer */
        $serializer = $this->container->get('alsciende_serializer.serializer');

        /* @var $validator RecursiveValidator */
        $validator = $this->container->get('validator');

        foreach($sources as $source) {
            $result = $serializer->importSource($source);
            foreach($result as $imported) {
                $entity = $imported['entity'];
                $errors = $validator->validate($entity);
                if(count($errors) > 0) {
                    $errorsString = (string) $errors;
                    throw new \Exception($errorsString);
                }
            }

            $manager->flush();
        }
    }

    public function getOrder()
    {
        return 2;
    }
}
