<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * A Deck, private (minorVersion > 0) or public (minorVersion == 0)
 * Decks are (mostly) immutable objects (exception: name and description of published decks)
 * Whenever a deck is created, a unique lineage id is generated for it, which will be shared by all its descendants
 * Whenever a deck is saved, a new Deck is created with an incremented minorVersion and the same Lineage
 * Whenever a deck is published, a new Deck is created with an incremented majorVersion and minorVersion=0 and the same Lineage
 * Whenever a deck is copied, a new Deck is created with a version of 0.1 and the same Lineage
 *
 * @ORM\Table(name="decks")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DeckRepository")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("alphabetical")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Deck
{

    /**
     * Unique identifier of the deck
     *
     * @var string
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMS\Expose
     */
    private $id;

    /**
     * Name of the deck
     *
     * @var string
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @JMS\Expose
     */
    private $name;

    /**
     * Markdown-formatted description of the deck
     *
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     * @JMS\Expose
     */
    private $description;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime",nullable=false)
     * @JMS\Expose
     * @JMS\Type("DateTime")
     */
    protected $createdAt;

    /**
     * The phoenixborn used by the deck
     *
     * @var Card
     * @ORM\ManyToOne(targetEntity="Card")
     * @ORM\JoinColumn(name="phoenixborn_code", referencedColumnName="code")
     */
    private $phoenixborn;

    /**
     * The cards used by the deck
     *
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="DeckCard", mappedBy="deck", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    private $deckCards;

    /**
     * The owner of the deck
     *
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * The number of likes, for a public deck
     *
     * @var integer
     * @ORM\Column(name="nb_likes", type="integer", nullable=true)
     * @JMS\Expose
     */
    private $nbLikes;

    /**
     * The major version of the deck. Incremented with each publication.
     *
     * @var integer
     * @ORM\Column(name="major_version", type="integer", nullable=false)
     */
    private $majorVersion;

    /**
     * The minor version of the deck. 0 for a public deck. Incremented with each save.
     *
     * @var integer
     * @ORM\Column(name="minor_version", type="integer", nullable=false)
     */
    private $minorVersion;

    /**
     * Whether the deck is published
     *
     * @var boolean
     * @ORM\Column(name="is_published", type="boolean", nullable=false)
     */
    private $isPublished;

    /**
     * A number indicating a problem with the deck
     *
     * @var integer
     * @ORM\Column(name="problem", type="integer", nullable=false)
     * @JMS\Expose
     */
    private $problem;

    /**
     * Unique identifier for different versions of a same deck by a single User
     *
     * @var string
     * @ORM\Column(name="lineage", type="string", nullable=false)
     * @JMS\Expose
     */
    private $lineage;

    /**
     * Identifier for all decks that share a common ancestor across Users
     *
     * @var string
     * @ORM\Column(name="genus", type="string", nullable=false)
     * @JMS\Expose
     */
    private $genus;

    function __construct ()
    {
        $this->deckCards = new \Doctrine\Common\Collections\ArrayCollection();
        $this->problem = \AppBundle\Service\DeckChecker::VALID_DECK;
    }

    function __toString ()
    {
        return sprintf("%s (%s)", $this->name, $this->id ?: "no id");
    }

    /**
     *
     * @return string
     */
    function getId ()
    {
        return $this->id;
    }

    /**
     * @var string
     * @return Deck
     */
    function setId (string $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     *
     * @return string
     */
    function getName ()
    {
        return $this->name;
    }

    /**
     *
     * @param string $name
     * @return Deck
     */
    function setName ($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Sets createdAt.
     *
     * @param  \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt (\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Returns createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt ()
    {
        return $this->createdAt;
    }

    /**
     *
     * @return Card
     */
    function getPhoenixborn ()
    {
        return $this->phoenixborn;
    }

    /**
     * @JMS\VirtualProperty
     * @return string
     */
    function getPhoenixbornCode ()
    {
        return $this->phoenixborn ? $this->phoenixborn->getCode() : null;
    }

    /**
     *
     * @param \AppBundle\Entity\Card $phoenixborn
     * @return Deck
     */
    function setPhoenixborn (Card $phoenixborn)
    {
        $this->phoenixborn = $phoenixborn;

        return $this;
    }

    /**
     *
     * @return \AppBundle\Model\CardSlotCollectionDecorator
     */
    function getDeckCards ()
    {
        return new \AppBundle\Model\CardSlotCollectionDecorator($this->deckCards->toArray());
    }

    /**
     *
     * @param \Doctrine\Common\Collections\Collection $deckCards
     * @return Deck
     */
    function setDeckCards (\Doctrine\Common\Collections\Collection $deckCards)
    {
        $this->deckCards = $deckCards;

        return $this;
    }

    /**
     *
     * @return Deck
     */
    function clearDeckCards ()
    {
        $this->deckCards = new \Doctrine\Common\Collections\ArrayCollection();

        return $this;
    }

    /**
     *
     * @param \AppBundle\Entity\DeckCard $deckCard
     * @return Deck
     */
    function addDeckCard (DeckCard $deckCard)
    {
        if (!$this->deckCards->contains($deckCard)) {
            $this->deckCards[] = $deckCard;
        }

        return $this;
    }

    /**
     *
     * @return User
     */
    function getUser ()
    {
        return $this->user;
    }

    /**
     * @JMS\VirtualProperty
     * @return string
     */
    function getUserId ()
    {
        return $this->user ? $this->user->getId() : null;
    }

    /**
     *
     * @param \AppBundle\Entity\User $user
     * @return Deck
     */
    function setUser (User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     *
     * @return integer
     */
    function getNbLikes ()
    {
        return $this->nbLikes;
    }

    /**
     *
     * @param integer $nbLikes
     * @return Deck
     */
    function setNbLikes ($nbLikes)
    {
        $this->nbLikes = $nbLikes;
        return $this;
    }


    /**
     *
     * @return string
     */
    function getDescription ()
    {
        return $this->description;
    }

    /**
     *
     * @param string $description
     * @return Deck
     */
    function setDescription ($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     *
     * @return integer
     */
    function getMajorVersion ()
    {
        return $this->majorVersion;
    }

    /**
     *
     * @return integer
     */
    function getMinorVersion ()
    {
        return $this->minorVersion;
    }

    /**
     *
     * @param integer $majorVersion
     * @return Deck
     */
    function setMajorVersion ($majorVersion)
    {
        $this->majorVersion = $majorVersion;
        return $this;
    }

    /**
     *
     * @param integer $minorVersion
     * @return Deck
     */
    function setMinorVersion ($minorVersion)
    {
        $this->minorVersion = $minorVersion;
        return $this;
    }

    /**
     * @JMS\VirtualProperty
     * @return string
     */
    function getVersion ()
    {
        return $this->majorVersion . "." . $this->minorVersion;
    }

    /**
     *
     * @return boolean
     */
    function getIsPublished ()
    {
        return $this->isPublished;
    }

    /**
     *
     * @param boolean $isPublished
     * @return Deck
     */
    function setIsPublished ($isPublished)
    {
        $this->isPublished = $isPublished;
        return $this;
    }

    /**
     * @JMS\VirtualProperty
     * @return array
     */
    function getCards ()
    {
        return $this->getDeckCards()->getContent();
    }

    /**
     * @JMS\VirtualProperty
     * @return array
     */
    function getDices ()
    {
        return $this->getDeckDices()->getContent();
    }

    /**
     *
     * @return integer
     */
    function getProblem ()
    {
        return $this->problem;
    }

    /**
     *
     * @param integer $problem
     * @return Deck
     */
    function setProblem ($problem)
    {
        $this->problem = $problem;
        return $this;
    }

    /**
     *
     * @return string
     */
    function getLineage ()
    {
        return $this->lineage;
    }

    /**
     *
     * @param string $lineage
     * @return Deck
     */
    function setLineage ($lineage)
    {
        $this->lineage = $lineage;
        return $this;
    }

    /**
     *
     * @return string
     */
    function getGenus ()
    {
        return $this->genus;
    }

    /**
     *
     * @param string $genus
     * @return Deck
     */
    function setGenus ($genus)
    {
        $this->genus = $genus;
        return $this;
    }


}
