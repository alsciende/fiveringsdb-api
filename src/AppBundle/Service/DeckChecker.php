<?php

namespace AppBundle\Service;

/**
 * Service to determine the validity/legality of a deck
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckChecker
{

    const VALID_DECK = 0;
    const INCLUDES_PHOENIXBORN = 2;
    const TOO_FEW_CARDS = 3;
    const TOO_MANY_CARDS = 4;
    const TOO_MANY_COPIES = 5;
    const INCLUDES_CONJURATION = 6;
    const FORBIDDEN_EXCLUSIVE = 7;
    const TOO_FEW_DICES = 8;
    const TOO_MANY_DICES = 9;

    /**
     *
     * @var DeckChecker\DeckCheckerInterface[]
     */
    private $deckCheckers;

    public function __construct ()
    {
        $this->deckCheckers = array();
    }

    /**
     * Called by the CompilerPass to get all the DeckCheckers in services.yml
     * 
     * @param \AppBundle\Service\DeckChecker\DeckCheckerInterface $deckChecker
     */
    public function addDeckChecker (DeckChecker\DeckCheckerInterface $deckChecker)
    {
        $this->deckCheckers[] = $deckChecker;
    }

    /**
     * Returns null if the deck is legal, or a string
     * 
     * @param \AppBundle\Entity\Deck $deck
     */
    public function check (\AppBundle\Entity\Deck $deck)
    {
        foreach ($this->deckCheckers as $deckChecker) {
            $result = $deckChecker->check($deck);
            if ($result) {
                return $result;
            }
        }

        return self::VALID_DECK;
    }

}
