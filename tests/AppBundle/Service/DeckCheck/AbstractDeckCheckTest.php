<?php

declare(strict_types=1);

namespace Tests\AppBundle\Service\DeckCheck;

use AppBundle\Entity\Card;
use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Service\DeckCheck\DeckCheckInterface;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractDeckCheckTest extends WebTestCase
{
    protected function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }

    public function assertCheck(DeckCheckInterface $checker, int $expectedValue, array $deckCards) {
        $this->assertEquals(
            $expectedValue,
            $checker->check(new CardSlotCollectionDecorator($deckCards), 'standard')
        );
    }

    protected function getCard($attributes)
    {
        $serializer = static::$kernel->getContainer()->get('jms_serializer');
        return $serializer->fromArray($attributes, Card::class);
    }
}
