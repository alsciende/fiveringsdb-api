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
    use DeckDataTrait;

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
