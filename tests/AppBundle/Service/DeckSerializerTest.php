<?php

declare(strict_types=1);

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Card;
use AppBundle\Entity\Deck;
use AppBundle\Entity\DeckCard;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Tests\AppBundle\DeckDataTrait;

class DeckSerializerTest extends KernelTestCase
{
    use DeckDataTrait;

    /** @var Container */
    private $container;

    public function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
    }

    public function testFromArray()
    {
        /** @var Serializer $serializer */
        $serializer = $this->container->get('jms_serializer');

        /** @var Deck $deck */
        $deck = $serializer->fromArray($this->deckData, Deck::class);

        $this->assertInstanceOf(Deck::class, $deck);
        $this->assertGreaterThan(0, $deck->getDeckCards()->count());
    }

    public function testToArray()
    {
        $deck = new Deck();
        $deck->setName('Test Deck');
        $deck->addDeckCard(new DeckCard((new Card())->setId('test-card'), 3));

        /** @var Serializer $serializer */
        $serializer = $this->container->get('jms_serializer');

        $data = $serializer->toArray($deck);

        $this->assertArrayHasKey('name', $data);
        $this->assertEquals(1, count($data['cards']));
    }
}
