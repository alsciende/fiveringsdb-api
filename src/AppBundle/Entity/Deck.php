<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var DeckSlots[]
     * 
     * @ORM\OneToMany(targetEntity="DeckSlot", mappedBy="deck", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    private $slots;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    

    function __construct ()
    {
        $this->slots = new \AppBundle\Model\CardSlotCollection();
    }

    function getId ()
    {
        return $this->id;
    }

    function getName ()
    {
        return $this->name;
    }

    function setName ($name)
    {
        $this->name = $name;
    }
    function getDeckSlots ()
    {
        return $this->deckSlots;
    }

    function setDeckSlots (array $deckSlots)
    {
        $this->deckSlots = $deckSlots;
    }

    function getUser ()
    {
        return $this->user;
    }

    function setUser (User $user)
    {
        $this->user = $user;
    }

}
