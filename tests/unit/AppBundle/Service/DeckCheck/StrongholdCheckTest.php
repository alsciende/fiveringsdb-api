<?php

declare(strict_types=1);

namespace Tests\Unit\AppBundle\Service\DeckCheck;

use AppBundle\Entity\Card;
use AppBundle\Entity\DeckCard;
use AppBundle\Service\DeckCheck\StrongholdCheck;
use AppBundle\Service\DeckValidator;

class StrongholdCheckTest extends AbstractDeckCheckTest
{
    function testTooFewStronghold()
    {
        $this->assertCheck(
            new StrongholdCheck(),
            DeckValidator::TOO_FEW_STRONGHOLD,
            [
            ]
        );
    }

    function testTooManyStronghold()
    {
        $this->assertCheck(
            new StrongholdCheck(),
            DeckValidator::TOO_MANY_STRONGHOLD,
            [
                new DeckCard(
                    $this->getCard(['type' => Card::TYPE_STRONGHOLD]),
                    2
                ),
            ]
        );
    }
}
