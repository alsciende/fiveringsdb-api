<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Format;
use AppBundle\Entity\User;
use AppBundle\Manager\DeckManager;
use AppBundle\Service\DeckSerializer;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\ExpressionLanguage\Tests\Node\Obj;
use Symfony\Component\Form\DataTransformerInterface;

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
        $format = $manager->getRepository(Format::class)->find('single-core');
        $user = $this->getReference('user-user');

        $this->loadDeck('Suggested Starting Crane Deck', $format, $user, [
            'shizuka-toshi' => 1,
            'seeker-of-air' => 1,
            'the-art-of-peace' => 1,
            'fertile-fields' => 1,
            'entrenched-position' => 1,
            'meditations-on-the-tao' => 1,
            'shameful-display' => 1,
            'asahina-artisan' => 1,
            'doji-whisperer' => 1,
            'brash-samurai' => 1,
            'cautious-scout' => 1,
            'doji-gift-giver' => 1,
            'savvy-politician' => 1,
            'daidoji-nerishma' => 1,
            'steward-of-law' => 1,
            'political-rival' => 1,
            'above-question' => 1,
            'duelist-training' => 1,
            'height-of-fashion' => 1,
            'the-perfect-gift' => 1,
            'voice-of-honor' => 1,
            'way-of-the-crane' => 1,
            'admit-defeat' => 1,
            'noble-sacrifice' => 1,
            'doji-challenger' => 1,
            'kakita-asami' => 1,
            'kakita-kaezin' => 1,
            'asahina-storyteller' => 1,
            'guest-of-honor' => 1,
            'doji-hotaru' => 1,
            'artisan-academy' => 1,
            'display-of-power' => 1,
            'seeker-of-knowledge' => 1,
            'magnificent-kimono' => 1,
            'pacifism' => 1,
            'know-the-world' => 1,
            'otomo-courtier' => 3,
            'seppun-guardsman' => 3,
            'miya-mystic' => 3,
            'seeker-initiate' => 2,
            'wandering-ronin' => 3,
            'favorable-ground' => 1,
            'imperial-storehouse' => 1,
            'fine-katana' => 2,
            'ornate-fan' => 2,
            'banzai' => 2,
            'contingency-plan' => 2,
            'court-games' => 2,
            'for-shame' => 2,
            'good-omen' => 1,
            'outwit' => 2,
        ]);

        $manager->flush();
    }

    public function loadDeck(string $name, Format $format, User $user, array $cards)
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