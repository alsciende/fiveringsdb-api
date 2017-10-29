<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Service\UserManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Loader\NativeLoader;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Description of LoadAliceData
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LoadAliceData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function load (ObjectManager $manager)
    {
        $loader = new NativeLoader();
        $objectSet = $loader->loadFile(__DIR__.'/fixtures.yml');

        foreach($objectSet->getObjects() as $reference => $object) {
            $manager->persist($object);
            $this->addReference($reference, $object);
        }

        $manager->flush();
    }

    public function getOrder ()
    {
        return 2;
    }

}
