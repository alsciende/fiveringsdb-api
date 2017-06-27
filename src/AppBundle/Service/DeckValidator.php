<?php

namespace AppBundle\Service;

use AppBundle\Entity\Deck;
use AppBundle\Model\CardSlotCollectionDecorator;
use AppBundle\Service\DeckChecker\DeckCheckerInterface;

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

    /** @var DeckCheckerInterface[] */
    private $deckCheckers;

    public function __construct ()
    {
        $this->deckCheckers = array();
    }

    /**
     * Called by the CompilerPass to get all the DeckCheckers in services.yml
     */
    public function addDeckChecker (DeckCheckerInterface $deckChecker)
    {
        $this->deckCheckers[] = $deckChecker;
    }

    public function getDeckCheckerCount()
    {
        return count($this->deckCheckers);
    }

    /**
     * Returns null if the deck is legal, or a string
     */
    public function check (CardSlotCollectionDecorator $deckCards)
    {
        foreach ($this->deckCheckers as $deckChecker) {
            $result = $deckChecker->check($deckCards);
            if ($result !== self::VALID_DECK) {
                return $result;
            }
        }

        return self::VALID_DECK;
    }

}
