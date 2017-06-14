<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use AppBundle\Manager\DeckManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Description of LoadDeckData
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LoadDeckData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    use ContainerAwareTrait;

    public function load (ObjectManager $manager)
    {
        return;

        /* @var $deckManager DeckManager */
        $deckManager = $this->container->get('app.deck_manager');
        $user = $this->getReference('user-user');

        $deckManager->createNewInitialDeck([
            "name" => "The Bloodwoods Queen",
            "phoenixborn_code" => "jessa-na-ni",
            "description" => "Pre-built Deck for Jessa Na Ni",
            "tags" => "prebuilt",
            "cards" => [
                "blood-archer" => 3,
                "blood-transfer" => 3,
                "cut-the-strings" => 3,
                "fear" => 3,
                "final-cry" => 3,
                "leech-warrior" => 3,
                "living-doll" => 3,
                "redirect" => 3,
                "summon-blood-puppet" => 3,
                "undying-heart" => 3,
            ],
            "dices" => [
                "ceremonial" => 5,
                "charm" => 5,
            ],
                ], $user);

        $manager->flush();
    }

    public function getOrder ()
    {
        return 4;
    }

}
