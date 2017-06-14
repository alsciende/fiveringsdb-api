<?php

namespace AppBundle\Entity;

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

    public function __construct (Deck $deck, Card $card, int $quantity)
    {
        $this->deck = $deck;
        $this->card = $card;
        $this->quantity = $quantity;
    }

    function getQuantity ()
    {
        return $this->quantity;
    }

    function getCard ()
    {
        return $this->card;
    }

    /**
     * @JMS\VirtualProperty
     * @return string
     */
    function getCardCode ()
    {
        return $this->card ? $this->card->getCode() : null;
    }

    function getDeck ()
    {
        return $this->deck;
    }

    function setQuantity ($quantity)
    {
        $this->quantity = $quantity;
    }

    function setCard (\AppBundle\Entity\Card $card)
    {
        $this->card = $card;
    }

    function setDeck (\AppBundle\Entity\Deck $deck)
    {
        $this->deck = $deck;
    }

    function getElement ()
    {
        return $this->card;
    }
}
