<?php

declare(strict_types=1);

namespace Tests\AppBundle\Service\DeckCheck;

use AppBundle\Entity\Card;
use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Service\DeckCheck\DeckCheckInterface;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

abstract class AbstractDeckCheckTest extends TestCase
{
    public function assertCheck(DeckCheckInterface $checker, int $expectedValue, array $deckCards) {
        $this->assertEquals(
            $expectedValue,
            $checker->check(new CardSlotCollectionDecorator($deckCards), 'standard')
        );
    }

    protected function getCard($attributes)
    {
        $serializer = SerializerBuilder::create()->build();
        return $serializer->fromArray($attributes, Card::class);
    }
}
