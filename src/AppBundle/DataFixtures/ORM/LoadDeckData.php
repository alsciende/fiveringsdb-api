<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Deck;
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
        /* @var $deckManager DeckManager */
        $deckManager = $this->container->get('app.deck_manager');
        $user = $this->getReference('user-user');

        $deck = $this->container->get('jms_serializer')->fromArray([
            'name' => 'Crane Deck',
            'description' => 'Crane deck from Core Set',
            'cards' => [
                'shizuka-toshi' => 1,
//                'ancestral-lands' => 1, // Earth Province
                'elemental-fury' => 1, // Water Province
                'the-art-of-peace' => 1, // Air Province
                'night-raid' => 1, // Fire Province
                'pilgrimage' => 1, // Void Province
                // start of Dynasty
                'artisan-academy' => 3,
                'asahina-artisan' => 3,
                'asahina-storyteller' => 3,
                'daidoji-nerishma' => 3,
                'doji-challenger' => 3,
                'doji-gift-giver' => 3,
                'doji-hotaru' => 3,
                'guest-of-honor' => 3,
                'kakita-asami' => 3,
                'kakita-kaezin' => 3,
                'otomo-courtier' => 3,
                'savvy-politician' => 3,
                'wandering-ronin' => 3,
                // start of Conflict
                'above-question' => 3,
                'admit-defeat' => 3,
                'banzai' => 3,
                'cloud-the-mind' => 3,
                'duelist-training' => 3,
                'fallen-in-battle' => 3,
                'for-shame' => 3,
                'good-omen' => 3,
                'height-of-fashion' => 3,
                'noble-sacrifice' => 3,
                'spies-at-court' => 3,
                'steward-of-law' => 3,
                'the-perfect-gift' => 3,
                'voice-of-honor' => 3,
                'way-of-the-crane' => 3,
            ]
        ], Deck::class);

        $deckManager->createNewInitialDeck($deck, $user);

        $manager->flush();

        $this->addReference('deck-crane', $deck);
    }

    public function getOrder ()
    {
        return 4;
    }

}
