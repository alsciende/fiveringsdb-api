<?php

namespace AppBundle\Service\DeckCheck;

use AppBundle\Entity\Card;
use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Model\CardSlotInterface;
use AppBundle\Service\DeckValidator;

/**
 * Description of ProvinceChecker
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ProvinceCheck implements DeckCheckInterface
{
    public function check(CardSlotCollectionDecorator $deckCards, string $format): int
    {
        $provinceSlots = $deckCards->filterByType(Card::TYPE_PROVINCE);

        if($provinceSlots->countCards() < 5) {
            return DeckValidator::TOO_FEW_PROVINCE;
        }

        if($provinceSlots->countCards() > 5) {
            return DeckValidator::TOO_MANY_PROVINCE;
        }

        if($provinceSlots->isDistinct() === false) {
            return DeckValidator::DUPLICATE_PROVINCE;
        }

        $provinceElements = array_unique(
            array_map(
                function (CardSlotInterface $slot) {
                    return $slot->getCard()->getElement();
                },
                $provinceSlots->toArray()
            )
        );

        if(count($provinceElements) < 5) {
            $seekerException = false;

            $role = $deckCards->findRole();
            if($role instanceof Card
                && $role->hasTrait('seeker')
                && count(array_diff($provinceElements, $role->getTraits())) === 3
            ) {
                $seekerException = true;
            }

            if($seekerException === false) {
                return DeckValidator::DUPLICATE_ELEMENT;
            }
        }

        $stronghold = $deckCards->findStronghold();
        if ($stronghold instanceof Card) {
            $clan = $stronghold->getClan();

            $offClanProvinceSlot = $provinceSlots->find(function ($slot) use ($clan) {
                /** @var CardSlotInterface $slot */
                return $slot->getCard()->getClan() !== 'neutral'
                    && $slot->getCard()->getClan() !== $clan;
            });

            if($offClanProvinceSlot !== null) {
                return DeckValidator::OFF_CLAN_PROVINCE;
            }
        }

        return DeckValidator::VALID_DECK;
    }
}