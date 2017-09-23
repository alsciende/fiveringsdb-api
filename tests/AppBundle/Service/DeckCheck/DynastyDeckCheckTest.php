<?php

declare(strict_types=1);

namespace Tests\AppBundle\Service\DeckCheck;

use AppBundle\Entity\Card;
use AppBundle\Entity\DeckCard;
use AppBundle\Service\DeckCheck\DynastyDeckCheck;
use AppBundle\Service\DeckValidator;

class DynastyDeckCheckTest extends AbstractDeckCheckTest
{
    function testTooFewDynasty()
    {
        $this->assertCheck(
            new DynastyDeckCheck(),
            DeckValidator::TOO_FEW_DYNASTY,
            [
                new DeckCard(
                    $this->getCard(['side' => Card::SIDE_DYNASTY]),
                    39
                ),
            ]
        );
    }

    function testTooManyDynasty()
    {
        $this->assertCheck(
            new DynastyDeckCheck(),
            DeckValidator::TOO_MANY_DYNASTY,
            [
                new DeckCard(
                    $this->getCard(['side' => Card::SIDE_DYNASTY]),
                    46
                ),
            ]
        );
    }

    function testOffClanDynasty()
    {
        $this->assertCheck(
            new DynastyDeckCheck(),
            DeckValidator::OFF_CLAN_DYNASTY,
            [
                new DeckCard(
                    $this->getCard(['side' => Card::SIDE_PROVINCE, 'type' => Card::TYPE_STRONGHOLD, 'clan' => Card::CLAN_CRAB, 'influence_pool' => 10]),
                    1
                ),
                new DeckCard(
                    $this->getCard(['side' => Card::SIDE_DYNASTY, 'clan' => Card::CLAN_CRAB]),
                    40
                ),
                new DeckCard(
                    $this->getCard(['side' => Card::SIDE_DYNASTY, 'clan' => Card::CLAN_CRANE]),
                    1
                ),
            ]
        );
    }
}
