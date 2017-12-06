<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Description of LoadSourceData
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LoadSourceData extends Fixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    use  ContainerAwareTrait;

    public function load (ObjectManager $manager)
    {
        $scanningService = $this->container->get('alsciende_serializer.scanning_service');

        $sources = $scanningService->findSources();

        $serializer = $this->container->get('alsciende_serializer.serializer');

        $validator = $this->container->get('validator');

        foreach ($sources as $source) {
            $result = $serializer->importSource($source);
            foreach ($result as $imported) {
                $entity = $imported['entity'];
                $errors = $validator->validate($entity);
                if (count($errors) > 0) {
                    $errorsString = (string) $errors;
                    throw new \Exception($errorsString);
                }
            }

            $manager->flush();
        }
    }

    public function getOrder ()
    {
        return 1;
    }
}
