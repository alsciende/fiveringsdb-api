<?php

namespace AppBundle\Entity;

use AppBundle\Model\CardSlotInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of DeckCard
 *
 * @ORM\Entity()
 * @ORM\Table(name="deck_cards")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckCard implements CardSlotInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var Card
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Card")
     * @ORM\JoinColumn(name="card_id", referencedColumnName="id")
     */
    private $card;

    /**
     * @var Deck
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Deck", inversedBy="deckCards")
     * @ORM\JoinColumn(name="deck_id", referencedColumnName="id")
     */
    private $deck;

    public function __construct (Card $card, int $quantity)
    {
        $this->card = $card;
        $this->quantity = $quantity;
    }

    function getQuantity (): int
    {
        return $this->quantity;
    }

    function getCard (): Card
    {
        return $this->card;
    }

    function getDeck (): ?Deck
    {
        return $this->deck;
    }

    function setQuantity ($quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    function setCard (Card $card): self
    {
        $this->card = $card;

        return $this;
    }

    function setDeck (Deck $deck): self
    {
        $this->deck = $deck;

        return $this;
    }
}
