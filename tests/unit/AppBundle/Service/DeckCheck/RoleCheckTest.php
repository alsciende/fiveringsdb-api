<?php

declare(strict_types=1);

namespace Tests\Unit\AppBundle\Service\DeckCheck;

use AppBundle\Entity\Card;
use AppBundle\Entity\DeckCard;
use AppBundle\Service\DeckCheck\RoleCheck;
use AppBundle\Service\DeckValidator;

class RoleCheckTest extends AbstractDeckCheckTest
{
    function testTooManyRole()
    {
        $this->assertCheck(
            new RoleCheck(),
            DeckValidator::TOO_MANY_ROLE,
            [
                new DeckCard(
                    $this->getCard(['type' => Card::TYPE_ROLE]),
                    2
                ),
            ]
        );
    }
}
