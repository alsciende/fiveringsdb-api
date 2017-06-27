<?php

namespace Tests\AppBundle\Service;

use AppBundle\Manager\DeckManager;
use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Service\DeckValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Description of DeckValidatorTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckValidatorTest extends KernelTestCase
{
    private $deck = [
        'shizuka-toshi' => 1,
        'keeper-of-air' => 1,
        'ancestral-lands' => 1, // Earth Province
        'elemental-fury' => 1, // Water Province
        'fertile-fields' => 1, // Air Province
        'night-raid' => 1, // Fire Province
        'pilgrimage' => 1, // Void Province
        // start of Dynasty
        'artisan-academy' => 2,
        'asahina-artisan' => 2,
        'asahina-storyteller' => 2,
        'brash-samurai' => 2,
        'cautious-scout' => 2,
        'daidoji-nerishma' => 2,
        'doji-challenger' => 2,
        'doji-gift-giver' => 2,
        'doji-hotaru' => 2,
        'doji-whisperer' => 2,
        'favorable-ground' => 2,
        'guest-of-honor' => 2,
        'imperial-storehouse' => 2,
        'kakita-asami' => 2,
        'kakita-kaezin' => 2,
        'keeper-initiate' => 2,
        'miya-mystic' => 2,
        'otomo-courtier' => 2,
        'savvy-politician' => 2,
        'seeker-initiate' => 2,
        'seppun-guardsman' => 2,
        'wandering-ronin' => 2,
        // start of Conflict
        'above-question' => 2,
        'admit-defeat' => 2,
        'assassination' => 2,
        'banzai' => 2,
        'charge' => 2,
        'cloud-the-mind' => 2,
        'contingency-plan' => 2,
        'court-games' => 2,
        'duelist-training' => 2,
        'fallen-in-battle' => 2,
        'fine-katana' => 2,
        'for-shame' => 2,
        'good-omen' => 2,
        'height-of-fashion' => 2,
        'noble-sacrifice' => 2,
        'ornate-fan' => 2,
        'rout' => 2,
        'spies-at-court' => 2,
        'steward-of-law' => 2,
        'the-perfect-gift' => 2,
        'voice-of-honor' => 2,
        'way-of-the-crane' => 2,
    ];

    /** @var Container */
    private $container;

    public function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
    }
    public function testValidDeck()
    {
        /** @var $manager DeckManager */
        $manager = $this->container->get('app.deck_manager');
        /** @var $validator DeckValidator */
        $validator = $this->container->get('app.deck_validator');

        $deckCards = $manager->denormalizeDeckCards($this->deck);
        $check = $validator->check(new CardSlotCollectionDecorator($deckCards));

        $this->assertEquals(
            DeckValidator::VALID_DECK,
            $check
        );
    }
}