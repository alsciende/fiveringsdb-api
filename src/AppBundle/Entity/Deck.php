<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Description of Deck

 * @ORM\Table(name="decks")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DeckRepository")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Deck
{

    use TimestampableEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var DeckSlots[]
     * 
     * @ORM\OneToMany(targetEntity="DeckSlot", mappedBy="deck", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    private $slots;

    function __construct ()
    {
        $this->slots = new \AppBundle\Model\CardSlotCollection();
    }

    function getId ()
    {
        return $this->id;
    }

    function getDeckSlots ()
    {
        return $this->deckSlots;
    }

    function setDeckSlots (array $deckSlots)
    {
        $this->deckSlots = $deckSlots;
    }

}
