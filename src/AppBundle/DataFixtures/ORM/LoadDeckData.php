<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use AppBundle\Manager\DeckManager;
use AppBundle\Service\DeckSerializer;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Yaml\Yaml;

/**
 * Description of LoadDeckData
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LoadDeckData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var DeckManager */
    private $manager;

    /** @var DeckSerializer */
    private $serializer;

    public function load (ObjectManager $manager)
    {
        $this->manager = $this->container->get('app.deck_manager');
        $this->serializer = $this->container->get('app.deck_serializer');
        $user = $this->getReference('user-user');

        $data = Yaml::parse(file_get_contents(__DIR__.'/fixtures/deck.yml'));

        foreach($data as $name => $cards) {
            $this->loadDeck($user, 'single-core', $name, $cards);
        }

        $manager->flush();
    }

    public function loadDeck(User $user, string $format, string $name, array $cards)
    {
        $deck = $this->serializer->deserialize([
            'name' => $name,
            'cards' => $cards,
        ]);
        $deck->setFormat($format);
        $deck->setUser($user);

        $strain = $this->manager->createNewStrain($user);
        $deck->setStrain($strain);
        $this->manager->persist($deck);
    }

    public function getOrder ()
    {
        return 5;
    }
}