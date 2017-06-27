<?php

namespace AppBundle\Entity;

use AppBundle\Model\SlotElementInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Description of DeckCard
 *
 * @ORM\Table(name="deck_cards")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("alphabetical")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckCard implements \AppBundle\Model\CardSlotInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     *
     * @JMS\Expose
     */
    private $quantity;

    /**
     * @var \AppBundle\Entity\Card
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Card")
     * @ORM\JoinColumn(name="card_code", referencedColumnName="code")
     */
    private $card;

    /**
     * @var \AppBundle\Entity\Deck
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

    /**
     * @JMS\VirtualProperty
     */
    function getCardCode (): string
    {
        return $this->card ? $this->card->getCode() : null;
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

    function getElement (): SlotElementInterface
    {
        return $this->card;
    }
}
