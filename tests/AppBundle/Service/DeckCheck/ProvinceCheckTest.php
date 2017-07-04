<?php

declare(strict_types=1);

namespace Tests\AppBundle\Service\DeckCheck;

use AppBundle\Entity\Card;
use AppBundle\Entity\DeckCard;
use AppBundle\Service\DeckCheck\ProvinceCheck;
use AppBundle\Service\DeckValidator;

class ProvinceCheckTest extends AbstractDeckCheckTest
{
    function testTooFewProvince()
    {
        $this->assertCheck(
            new ProvinceCheck(),
            DeckValidator::TOO_FEW_PROVINCE,
            [
                new DeckCard(
                    $this->getCard(['type' => Card::TYPE_PROVINCE]),
                    4
                ),
            ]
        );
    }

    function testTooManyProvince()
    {
        $this->assertCheck(
            new ProvinceCheck(),
            DeckValidator::TOO_MANY_PROVINCE,
            [
                new DeckCard(
                    $this->getCard(['type' => Card::TYPE_PROVINCE]),
                    6
                ),
            ]
        );
    }

    function testDuplicateElement()
    {
        $this->assertCheck(
            new ProvinceCheck(),
            DeckValidator::DUPLICATE_ELEMENT,
            [
                new DeckCard(
                    $this->getCard(['type' => Card::TYPE_PROVINCE, 'element' => Card::ELEMENT_AIR]),
                    1
                ),
                new DeckCard(
                    $this->getCard(['type' => Card::TYPE_PROVINCE, 'element' => Card::ELEMENT_EARTH]),
                    1
                ),
                new DeckCard(
                    $this->getCard(['type' => Card::TYPE_PROVINCE, 'element' => Card::ELEMENT_FIRE]),
                    1
                ),
                new DeckCard(
                    $this->getCard(['type' => Card::TYPE_PROVINCE, 'element' => Card::ELEMENT_VOID]),
                    1
                ),
                new DeckCard(
                    $this->getCard(['type' => Card::TYPE_PROVINCE, 'element' => Card::ELEMENT_VOID]),
                    1
                ),
            ]
        );
    }

    function testOffClanProvince()
    {
        $this->assertCheck(
            new ProvinceCheck(),
            DeckValidator::OFF_CLAN_PROVINCE,
            [
                new DeckCard(
                    $this->getCard(['type' => Card::TYPE_STRONGHOLD, 'clan' => Card::CLAN_CRAB]),
                    1
                ),
                new DeckCard(
                    $this->getCard(['type' => Card::TYPE_PROVINCE, 'element' => Card::ELEMENT_AIR, 'clan' => Card::CLAN_CRAB]),
                    1
                ),
                new DeckCard(
                    $this->getCard(['type' => Card::TYPE_PROVINCE, 'element' => Card::ELEMENT_EARTH, 'clan' => Card::CLAN_CRAB]),
                    1
                ),
                new DeckCard(
                    $this->getCard(['type' => Card::TYPE_PROVINCE, 'element' => Card::ELEMENT_FIRE, 'clan' => Card::CLAN_CRAB]),
                    1
                ),
                new DeckCard(
                    $this->getCard(['type' => Card::TYPE_PROVINCE, 'element' => Card::ELEMENT_VOID, 'clan' => Card::CLAN_CRAB]),
                    1
                ),
                new DeckCard(
                    $this->getCard(['type' => Card::TYPE_PROVINCE, 'element' => Card::ELEMENT_WATER, 'clan' => Card::CLAN_CRANE]),
                    1
                ),
            ]
        );
    }
}
