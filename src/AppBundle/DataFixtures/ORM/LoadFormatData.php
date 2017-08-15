<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Format;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Description of LoadFormatData
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LoadFormatData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function load (ObjectManager $manager)
    {
        $this->loadFormat($manager, 'standard', 'Sanctioned Tournament Deck Customization Rules');
        $this->loadFormat($manager, 'single-core', 'Adapted Rules for Out-of-Box Play');
    }

    private function loadFormat(ObjectManager $manager, string $id, string $description)
    {
        $format = new Format();
        $format->setId($id);
        $format->setDescription($description);
        $manager->persist($format);
        $manager->flush();
        $this->addReference("format-$id", $format);
    }

    public function getOrder ()
    {
        return 4;
    }
}