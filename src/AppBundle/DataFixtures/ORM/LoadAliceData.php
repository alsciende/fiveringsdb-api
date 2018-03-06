<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Loader\NativeLoader;

/**
 * Description of LoadAliceData
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LoadAliceData extends Fixture implements OrderedFixtureInterface
{
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
