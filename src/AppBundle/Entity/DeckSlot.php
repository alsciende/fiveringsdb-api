<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Description of DeckSlot
 *
 * @ORM\Table(name="deck_slots")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DeckSlotRepository")
 * 
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckSlot implements \AppBundle\Model\CardSlotInterface
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var \AppBundle\Entity\Card
     *
     * @ORM\ManyToOne(targetEntity="Card", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="card_code", referencedColumnName="code")
     */
    private $card;

    /**
     * @var \AppBundle\Entity\Deck
     *
     * @ORM\ManyToOne(targetEntity="Deck", fetch="EXTRA_LAZY", inversedBy="slots")
     * @ORM\JoinColumn(name="deck_id", referencedColumnName="id")
     */
    private $deck;

    function getId ()
    {
        return $this->id;
    }

    function getQuantity ()
    {
        return $this->quantity;
    }

    function getCard ()
    {
        return $this->card;
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

}
