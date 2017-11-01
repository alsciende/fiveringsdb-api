<?php

declare(strict_types=1);

namespace Tests\Unit\AppBundle\Service\DeckCheck;

use AppBundle\Entity\DeckCard;
use AppBundle\Service\DeckCheck\CardCopiesCheck;
use AppBundle\Service\DeckValidator;

class CardCopiesCheckerTest extends AbstractDeckCheckTest
{
    function testTooManyCopies()
    {
        $this->assertCheck(
            new CardCopiesCheck(),
            DeckValidator::TOO_MANY_COPIES,
            [
                new DeckCard(
                    $this->getCard(['deck_limit' => 3]),
                    4
                ),
            ]
        );
    }
}
