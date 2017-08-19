<?php

namespace AppBundle\Service;

use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Service\DeckCheck\DeckCheckInterface;

/**
 * Service to determine the validity/legality of a deck
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckValidator
{
    const VALID_DECK = 0;
    const TOO_MANY_COPIES = 1;
    const TOO_FEW_STRONGHOLD = 2;
    const TOO_MANY_STRONGHOLD = 3;
    const TOO_MANY_ROLE = 4;
    const TOO_FEW_DYNASTY = 5;
    const TOO_MANY_DYNASTY = 6;
    const OFF_CLAN_DYNASTY = 7;
    const TOO_FEW_CONFLICT = 8;
    const TOO_MANY_CONFLICT = 9;
    const NOT_ENOUGH_INFLUENCE = 10;
    const TOO_MANY_OFF_CLANS = 11;
    const TOO_MANY_CHARACTER_IN_CONFLICT = 12;
    const TOO_FEW_PROVINCE = 13;
    const TOO_MANY_PROVINCE = 14;
    const DUPLICATE_ELEMENT = 15;
    const OFF_CLAN_PROVINCE = 16;
    const DUPLICATE_PROVINCE = 17;
    const FORBIDDEN_SPLASH = 18;

    /** @var DeckCheckInterface[] */
    private $DeckChecks;

    public function __construct ()
    {
        $this->DeckChecks = array();
    }

    /**
     * Called by the CompilerPass to get all the DeckChecks in services.yml
     */
    public function addDeckCheck (DeckCheckInterface $DeckCheck)
    {
        $this->DeckChecks[] = $DeckCheck;
    }

    public function getDeckCheckCount()
    {
        return count($this->DeckChecks);
    }

    /**
     * Returns null if the deck is legal, or a string
     */
    public function check (CardSlotCollectionDecorator $deckCards, string $format)
    {
        foreach ($this->DeckChecks as $DeckCheck) {
            $result = $DeckCheck->check($deckCards, $format);
            if ($result !== self::VALID_DECK) {
                return $result;
            }
        }

        return self::VALID_DECK;
    }

}
